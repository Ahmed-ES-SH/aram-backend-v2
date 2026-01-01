<?php

namespace App\Http\Controllers;

use App\Http\Services\AppointmentResponseService;
use App\Http\Services\AppointmentService;
use App\Http\Services\CancelAppointmentService;
use App\Http\Traits\ApiResponse;
use App\Models\Appointment;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{

    use ApiResponse;




    // available times for users to show the times he can selected
    public function getAvailableTimes(Request $request, Organization $organization)
    {
        try {
            $date = $request->input('date', now()->toDateString());
            $interval = $request->input('interval', 30); // minutes

            $openAt = $organization->open_at;
            $closeAt = $organization->close_at;

            if (!$openAt || !$closeAt) {
                return $this->errorResponse([
                    'ar' => 'لم يتم تحديد ساعات العمل لهذا المركز.',
                    'en' => 'Working hours are not set for this organization.'
                ], 400);
            }

            $start = Carbon::parse("$date $openAt");
            $end = Carbon::parse("$date $closeAt");

            // Fetch booked times
            $bookedAppointments = Appointment::where('organization_id', $organization->id)
                ->whereDate('start_time', $date)
                ->get(['start_time', 'end_time']);

            $available = [];
            $current = $start->copy();

            while ($current < $end) {
                $slotStart = $current->copy();
                $slotEnd = $current->copy()->addMinutes($interval);

                // check if the slot overlaps with any existing appointment
                $isBooked = $bookedAppointments->contains(function ($appointment) use ($slotStart, $slotEnd) {
                    return !($slotEnd <= $appointment->start_time || $slotStart >= $appointment->end_time);
                });

                if (!$isBooked) {
                    $available[] = $slotStart->format('H:i');
                }

                $current->addMinutes($interval);
            }

            $data = [
                'date' => $date,
                'available_times' => $available
            ];

            return $this->successResponse($data, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getAllTimes(Request $request, Organization $organization)
    {
        try {
            $date = $request->input('date', now()->toDateString());
            $interval = (int) $request->input('interval', 30);

            $openAt = $organization->open_at;
            $closeAt = $organization->close_at;

            if (!$openAt || !$closeAt) {
                return $this->errorResponse([
                    'ar' => 'لم يتم تحديد ساعات العمل لهذا المركز.',
                    'en' => 'Working hours are not set for this organization.'
                ], 400);
            }

            $start = Carbon::parse("$date $openAt");
            $end   = Carbon::parse("$date $closeAt");

            // جلب المواعيد التي قد تتداخل مع نافذة العمل
            $bookedAppointments = Appointment::where('organization_id', $organization->id)
                ->where('start_time', '<', $end) // يبدأ قبل نهاية العمل
                ->where(function ($q) use ($start) {
                    // وينتهي بعد بداية العمل أو ليس له end_time
                    $q->where('end_time', '>', $start)
                        ->orWhereNull('end_time');
                })
                ->get(['id', 'start_time', 'end_time', 'user_id']);

            // حوّل إلى Carbons واعتبر end_time = start + 1 hour إذا كانت null
            $booked = $bookedAppointments->map(function ($a) use ($end) {
                $s = Carbon::parse($a->start_time);
                // إذا لم يوجد end_time نفترض ساعة واحدة فقط
                $e = $a->end_time
                    ? Carbon::parse($a->end_time)
                    : $s->copy()->addHour();

                // لا نجعل الانتهاء يتجاوز وقت إغلاق المركز (clamp)
                if ($e->greaterThan($end)) {
                    $e = $end->copy();
                }

                return [
                    'id' => $a->id,
                    'start' => $s,
                    'end' => $e,
                ];
            });

            $all = [];
            $current = $start->copy();

            while ($current < $end) {
                $slotStart = $current->copy();
                $slotEnd = $current->copy()->addMinutes($interval);

                if ($slotStart >= $end) break;

                if ($slotEnd > $end) {
                    $slotEnd = $end->copy();
                }

                // التداخل: NOT (slotEnd <= appt.start OR slotStart >= appt.end)
                $isBooked = $booked->first(function ($appt) use ($slotStart, $slotEnd) {
                    return ! ($slotEnd <= $appt['start'] || $slotStart >= $appt['end']);
                }) !== null;

                $all[] = [
                    'time' => $slotStart->format('H:i'),
                    'status' => $isBooked ? 'booked' : 'available',
                ];

                $current->addMinutes($interval);
            }

            return $this->successResponse([
                'date' => $date,
                'all_times' => $all
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getAllAppointments(Request $request, Organization $organization)
    {
        try {
            // التحقق من أن المركز (organization) تم تحديده
            $organizationId = $organization->id;

            if (!$organizationId) {
                return $this->errorResponse('organization_id is required', 400);
            }

            // جلب جميع المواعيد الخاصة بالمركز المحدد
            $appointments = Appointment::where('organization_id', $organizationId)
                ->orderBy('start_time', 'asc')
                ->get();

            if ($appointments->isEmpty()) {
                return $this->noContentResponse();
            }

            // تحويل البيانات إلى شكل منسّق (grouped by day)
            $grouped = $appointments->groupBy(function ($appointment) {
                return Carbon::parse($appointment->start_time)->format('Y-m-d');
            })->map(function ($items, $day) {
                return [
                    'book_day' => $day,
                    'times' => $items->map(function ($item) {
                        return [
                            'time' => Carbon::parse($item->start_time)->format('H:i'),
                            'status' => $item->status ?? 'pending',
                        ];
                    })->values(),
                ];
            })->values();

            return $this->successResponse([
                'data' => $grouped,
                'count' => $appointments->count(),
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function index(Request $request, string $type, int $id)
    {
        try {
            // Validate type
            if (!in_array($type, ['user', 'organization'])) {
                return $this->errorResponse([
                    'ar' => 'النوع غير صالح، يجب أن يكون user أو organization.',
                    'en' => 'Invalid type, must be user or organization.'
                ], 400);
            }

            // Base query
            $query = Appointment::query()
                ->with(['user:id,name,image,email', 'organization:id,title,logo,email'])
                ->when($type === 'user', fn($q) => $q->where('user_id', $id))
                ->when($type === 'organization', fn($q) => $q->where('organization_id', $id));

            // Optional filters
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->has('date')) {
                $query->whereDate('start_time', $request->get('date'));
            }

            // Sorting and pagination
            $appointments = $query
                ->orderByDesc('start_time')
                ->paginate(10);

            // Response
            return $this->paginationResponse($appointments, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function store(Request $request, Organization $organization, AppointmentService $appointmentService)
    {
        try {
            $result = $appointmentService->create($request->all(), $organization);

            if (!$result['success']) {
                return $this->errorResponse($result['errors'], $result['code']);
            }

            return $this->successResponse([
                'ar' => 'تم إرسال طلب الحجز بنجاح.',
                'en' => 'Booking request sent successfully.',
                'appointment' => $result['appointment']
            ], 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function cancelAppointmentsByOwner(Request $request, Organization $organization)
    {
        try {
            // التحقق من وجود المواعيد في الطلب
            if (!$request->has('appointments')) {
                return $this->errorResponse('No appointments provided', 400);
            }

            // تحويل JSON إلى مصفوفة PHP
            $appointments = is_array($request->appointments)
                ? $request->appointments
                : json_decode($request->appointments, true);

            if (empty($appointments)) {
                return $this->errorResponse('Appointments list is empty', 400);
            }

            $insertData = [];
            $now = now();

            foreach ($appointments as $appointment) {
                // التحقق من الحقول الأساسية
                if (
                    empty($appointment['organization_id']) ||
                    empty($appointment['start_time']) ||
                    empty($appointment['end_time'])
                ) {
                    continue; // تجاهل أي موعد ناقص البيانات الأساسية
                }

                // التأكد من عدم وجود موعد بنفس start/end لنفس المركز
                $exists = Appointment::where('organization_id', $appointment['organization_id'])
                    ->where('start_time', $appointment['start_time'])
                    ->where('end_time', $appointment['end_time'])
                    ->exists();

                if ($exists) {
                    continue; // تخطي هذا الموعد لأنه مكرر
                }

                $insertData[] = [
                    'user_id' => null,
                    'organization_id' => $appointment['organization_id'],
                    'start_time' => $appointment['start_time'],
                    'end_time' => $appointment['end_time'],
                    'price' => null,
                    'is_paid' => 0,
                    'status' => 'cancelled_by_org',
                    'user_notes' => "تم التعليق من قبل المركز نفسة .",
                    'organization_notes' => "تم التعليق من قبل المركز نفسة .",
                    'confirmed_at' => null,
                    'rejected_at' => null,
                    'cancelled_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // إذا لم يكن هناك مواعيد جديدة، أعد رسالة مناسبة
            if (empty($insertData)) {
                return $this->errorResponse('No new appointments to insert', 409);
            }

            // إدخال جميع المواعيد دفعة واحدة
            Appointment::insert($insertData);

            return $this->successResponse([
                'message' => 'Appointments added successfully',
                'inserted_count' => count($insertData),
            ], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function respond(Request $request, Organization $organization, Appointment $appointment, AppointmentResponseService $service)
    {
        $result = $service->respondToAppointment($appointment, $request->all(), $organization);

        if (!$result['success']) {
            return $this->errorResponse($result['errors'], $result['code']);
        }

        return $this->successResponse($result['appointment'], 200);
    }

    public function cancel(Request $request, CancelAppointmentService $appointmentService)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'cancler_id' => 'required|integer',
            'cancler_type' => 'required|in:user,organization',
        ]);

        $result = $appointmentService->cancelAppointment($validated);

        if ($result['success']) {
            return $this->successResponse($result['message'], 200);
        }

        return $this->errorResponse($result['message'], 403);
    }



    public function destroy(Request $request)
    {
        try {
            // ✅ Validate incoming data
            $validated = $request->validate([
                'appointment_id' => 'required|exists:appointments,id',
                'deleter_id' => 'required|integer',
                'deleter_type' => 'required|in:user,organization',
            ]);

            $appointment = Appointment::findOrFail($validated['appointment_id']);

            // ✅ Ensure deleter is one of the parties involved in the appointment
            $isAuthorized =
                ($validated['deleter_type'] === 'user' && $validated['deleter_id'] == $appointment->user_id) ||
                ($validated['deleter_type'] === 'organization' && $validated['deleter_id'] == $appointment->organization_id);

            if (! $isAuthorized) {
                return $this->errorResponse([
                    'ar' => 'غير مصرح لك بحذف هذا الموعد.',
                    'en' => 'You are not authorized to delete this appointment.'
                ], 403);
            }

            // ✅ Optional: Prevent deletion of active (pending/confirmed) appointments
            if (in_array($appointment->status, ['pending', 'confirmed'])) {
                return $this->errorResponse([
                    'ar' => 'لا يمكن حذف المواعيد النشطة، قم بإلغائها أولاً.',
                    'en' => 'You cannot delete active appointments. Please cancel them first.'
                ], 400);
            }

            // ✅ Perform deletion
            $appointment->delete();

            return $this->successResponse([
                'ar' => 'تم حذف الموعد بنجاح.',
                'en' => 'The appointment was deleted successfully.',
                'appointment_id' => $appointment->id
            ], 200);
        } catch (Exception $e) {

            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
