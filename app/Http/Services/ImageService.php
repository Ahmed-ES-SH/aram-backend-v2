<?php

namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * رفع وتحديث صورة المستخدم
     *
     * @param  Request $request
     * @param  $user
     * @param  string $storagePath
     * @return void
     */



    public function ImageUploaderwithvariable(Request $request, $model, string $storagePath = 'images/users', $variable = 'image')
    {
        if ($request->hasFile($variable)) {
            $imageFile = $request->file($variable);

            // Generate unique filename
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $imageFile->getClientOriginalExtension();
            $filename = $originalName . '_' . uniqid() . '.' . $extension;

            // Move the file to public path
            $imageFile->move(public_path($storagePath), $filename);
            $fullImagePath = url('/') . '/' . $storagePath . '/' . $filename;

            // Handle based on type: model or relation
            if ($model instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                // Add new image record in related table
                $model->create(['image' => $fullImagePath]);
            } else {
                // Handle single image column
                $columnName = $variable;

                // Delete old image if exists
                $old_image = $model->{$columnName};
                if ($old_image) {
                    $old_image_name = basename(parse_url($old_image, PHP_URL_PATH));
                    $file_path = public_path($storagePath . '/' . $old_image_name);
                    if (File::exists($file_path)) {
                        File::delete($file_path);
                    }
                }

                // Update image column
                $model->{$columnName} = $fullImagePath;
                $model->save();
            }
        } else {
            return 'file not found';
        }
    }




    // public function ImageUploaderwithvariableWithLogo(Request $request, $model, string $storagePath = 'images/users', $variable = 'image')
    // {
    //     if ($request->hasFile($variable)) {
    //         $imageFile = $request->file($variable);

    //         // Generate unique filename
    //         $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
    //         $extension = $imageFile->getClientOriginalExtension();
    //         $filename = $originalName . '_' . uniqid() . '.' . $extension;

    //         // Create full path for saving
    //         $fullPath = public_path($storagePath . '/' . $filename);

    //         // Process image with Intervention/Image
    //         $img = Image::make($imageFile);



    //         // Save the processed image
    //         $img->save($fullPath);

    //         $fullImagePath = url('/') . '/' . $storagePath . '/' . $filename;

    //         // Handle based on type: model or relation
    //         if ($model instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
    //             // Add new image record in related table
    //             $model->create(['image' => $fullImagePath]);
    //         } else {
    //             // Handle single image column
    //             $columnName = $variable;

    //             // Delete old image if exists
    //             $old_image = $model->{$columnName};
    //             if ($old_image) {
    //                 $old_image_name = basename(parse_url($old_image, PHP_URL_PATH));
    //                 $file_path = public_path($storagePath . '/' . $old_image_name);
    //                 if (\Illuminate\Support\Facades\File::exists($file_path)) {
    //                     \Illuminate\Support\Facades\File::delete($file_path);
    //                 }
    //             }

    //             // Update image column
    //             $model->{$columnName} = $fullImagePath;
    //             $model->save();
    //         }

    //         return $fullImagePath;
    //     } else {
    //         return 'file not found';
    //     }
    // }




    public function deleteOldImage($model, $storagePath)
    {
        if ($model) {
            $old_image = $model->image;
            $old_icon = $model->logo;

            if ($old_icon) {
                $oldIconName = basename(parse_url($old_icon, PHP_URL_PATH));
                $filePath = public_path($storagePath . '/' . $oldIconName);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
            if ($old_image) {
                // استخراج اسم الصورة من الرابط
                $oldImageName = basename(parse_url($old_image, PHP_URL_PATH));
                // تحديد المسار الفعلي للصورة في الخادم
                $filePath = public_path($storagePath . '/' . $oldImageName);

                // التحقق إذا كانت الصورة موجودة ثم حذفها
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
        }
    }



    public function uploadChatAttachment(UploadedFile $file, $model, string $storagePath = 'attachments/messages', string $variable = 'attachment'): ?string
    {
        // حذف المرفق القديم إن وجد
        $old_file = $model->{$variable};
        if ($old_file) {
            $old_file_name = basename(parse_url($old_file, PHP_URL_PATH));
            $file_path = public_path($storagePath . '/' . $old_file_name);
            if (File::exists($file_path)) {
                File::delete($file_path);
            }
        }

        // اسم جديد للملف
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $filename = $originalName . '_' . uniqid() . '.' . $extension;

        // حفظ الملف
        $file->move(public_path($storagePath), $filename);

        // حفظ الرابط في قاعدة البيانات
        $model->{$variable} = url('/') . '/' . $storagePath . '/' . $filename;
        $model->save();

        return $model->{$variable};
    }



    public function deleteChatAttachment($model, string $variable = 'attachment'): bool
    {
        if ($model->{$variable}) {
            // -------------------------
            // استخراج اسم الملف من الرابط المخزن
            // -------------------------
            $fileName = basename(parse_url($model->{$variable}, PHP_URL_PATH));
            $filePath = public_path('attachments/messages/' . $fileName); // تحديث المسار حسب الحاجة

            // -------------------------
            // حذف الملف من التخزين
            // -------------------------
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            // -------------------------
            // إزالة الرابط من قاعدة البيانات
            // -------------------------
            $model->{$variable} = null;
            $model->save();

            return true; // تم الحذف بنجاح
        }

        return false; // لا يوجد مرفق للحذف
    }
}
