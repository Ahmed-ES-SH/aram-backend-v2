<?php

use App\Http\Controllers\AboutController; // wait
use App\Http\Controllers\ServicePageController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ArticleCategoryController; // wait
use App\Http\Controllers\ArticleCommentController; // wait
use App\Http\Controllers\ArticleController; // wait
use App\Http\Controllers\ArticleInteractionsController; // wait
use App\Http\Controllers\AuthController; // wait
use App\Http\Controllers\CardCategoryController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CategoryController; // wait
use App\Http\Controllers\ContactMessageController; // wait
use App\Http\Controllers\ConversationController; // wait
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardMainPageController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\FooterLinkController; // wait
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\MemberController; // wait
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController; // wait
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationPrivacyPolicyController;
use App\Http\Controllers\QuestionAnswerController; // wait
use App\Http\Controllers\SocialAccountController; // wait
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TermsConditionController; // wait
use App\Http\Controllers\OrganizationTermsConditionController; // wait
use App\Http\Controllers\PrivacyPolicyController; // wait
use App\Http\Controllers\PromoterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController; // wait
use App\Http\Controllers\WalletController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\OrganizationReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewLikesCheckController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\WithdrawRequestController;
use App\Http\Controllers\OwnedCardController;
use App\Http\Controllers\PromoterRatioController;
use App\Http\Controllers\PromoterTrackingController;
use App\Http\Controllers\PromotionActivityController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\VariableDataController;
use App\Http\Controllers\WebsiteVideoController;
use App\Http\Controllers\ServiceTrackingController;
use App\Http\Controllers\ServiceFormController;
use App\Http\Controllers\ServiceFormFieldController;
use App\Http\Controllers\ServiceFormSubmissionController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\ServicePageContactMessageController;
use App\Http\Controllers\TempUploadController;
use Illuminate\Support\Facades\Route;


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// ------------------------------
// Service Pages API v1 Routes --
// ------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Route::controller(ServicePageController::class)->group(function () {
    // Public routes
    Route::get('/service-pages', 'index');
    Route::get('/service-pages/{id}', 'show');
});


// ----------------------------------------
//  Service page contact messages  ------
// ----------------------------------------


Route::post('/add-service-message', [ServicePageContactMessageController::class, 'store']);

// ----------------------------------------
//  Service Form Schema (Public) ----------
// ----------------------------------------

Route::get('/service/{slug}/form', [ServiceFormController::class, 'getFormSchema']);


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// ------------------------------
// start public Routes ----------
// ------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



// ----------------------------------------
//  Footer Link Routes ----------------
// ----------------------------------------

Route::controller(FooterLinkController::class)->group(function () {
    Route::get('/all-lists', 'getLinksByList');
});



// ----------------------------------------
//  Variable Data Routes ----------------
// ----------------------------------------


Route::get('/get-variable-data', [VariableDataController::class, 'getVariablesData']);
Route::get('/get-main-sections', [VariableDataController::class, 'getMainSections']);


// --------------------------------
//  Social Contact Info Routes ----
// --------------------------------


Route::controller(SocialAccountController::class)->group(function () {
    Route::get('/social-contact-info', 'getAccounts');
    Route::get('/get-whatsapp-number', 'getWhatsappNumber');
});




// --------------------------------
//  Website Video Routes ----
// --------------------------------

Route::get('/get-video', [WebsiteVideoController::class, 'getVideo']);
Route::get('/get-main-page-videos', [WebsiteVideoController::class, 'getMainPageVideos']);

// ----------------------------------------
//  home page Routes ----------------
// ----------------------------------------
Route::get('/get-section/{id}', [HomePageController::class, 'getSection']);
Route::get('/active-hero-section', [HomePageController::class, 'activeHeroSection']);



// ----------------------------------------
//  Slides Routes ----------------
// ----------------------------------------

Route::get('/active-slides', [SlideController::class, 'activeSlides']);


// ----------------------------------------
//  verify email Routes ----------------
// ----------------------------------------

Route::post('/send-verify-email', [UserController::class, 'sendVerifyEmail']);
Route::get('/verify-email/{id}', [UserController::class, 'verifyEmail']);


// ----------------------------------------
//  promotion visit Routes ----------------
// ----------------------------------------

Route::post('/promotion-visit', [PromotionActivityController::class, 'store']);


// ----------------------------------------
//  main Categories Routes ----------------
// ----------------------------------------

Route::get('/public-categories', [CategoryController::class, 'publicCategories']);
Route::get('/all-public-categories', [CategoryController::class, 'AllPublicCategories']);
Route::get('/categories', [CategoryController::class, 'index']);

// ----------------------------------------
//  main Sub Categories Routes ------------
// ----------------------------------------

Route::get('/sub-categories-by-parent', [SubCategoryController::class, 'getSubCategoriesByParent']);
Route::get('/public-sub-categories', [SubCategoryController::class, 'publicSubCategories']);
Route::get('/all-public-sub-categories', [SubCategoryController::class, 'AllSubCategories']);
Route::get('/sub-categories', [SubCategoryController::class, 'index']);


// ----------------------------------------
//  Services Routes ------------
// ----------------------------------------




// ----------------------------------------
//  Card Categories Routes ----------------
// ----------------------------------------

Route::get('/public-card-categories', [CardCategoryController::class, 'publicCategories']);
Route::get('/all-card-categories', [CardCategoryController::class, 'AllCategories']);
Route::get('/all-card-public-categories', [CardCategoryController::class, 'AllPublicCategories']);
Route::get('/card-categories', [CardCategoryController::class, 'index']);


// ----------------------------------------
//  Service Categories Routes ----------------
// ----------------------------------------

Route::get('/public-service-categories', [ServiceCategoryController::class, 'publicCategories']);
Route::get('/all-service-categories', [ServiceCategoryController::class, 'AllCategories']);
Route::get('/all-service-public-categories', [ServiceCategoryController::class, 'AllPublicCategories']);
Route::get('/service-categories', [ServiceCategoryController::class, 'index']);

// ----------------------------------------
//  Currencies  Routes --------------------
// ----------------------------------------

Route::get('/currencies', [CurrencyController::class, 'index']);

// ----------------------------------------
//  send sms   Routes ---------------------
// ----------------------------------------
Route::post('/internal/send-sms', [SMSController::class, 'send'])->middleware('check.api.key');


// ----------------------------------------
//  Articles Categories Routes ------------
// ----------------------------------------

Route::get('/public-article-categories', [ArticleCategoryController::class, 'publicCategories']);
Route::get('/all-public-article-categories', [ArticleCategoryController::class, 'allpublicCategories']);
Route::get('/article-categories', [ArticleCategoryController::class, 'index']);
Route::get('/all-article-categories', [ArticleCategoryController::class, 'allCategories']);


// ----------------------------------------
//  keywords Routes ------------
// ----------------------------------------

Route::get('/keywords', [KeywordController::class, 'index']);


// ----------------------------------------
//  Cards Routes --------------------------
// ----------------------------------------


Route::controller(CardController::class)->group(function () {
    Route::get('/public-cards', 'publicCards');
    Route::get('/eight-public-cards', 'EightCards');
    Route::get('/get-card/{id}', 'show');
});



// ----------------------------------------
//  Organizations Routes ------------------
// ----------------------------------------


Route::controller(OrganizationController::class)->group(function () {
    Route::get('/public-organizations', 'publishedOrganizations');
    Route::get('/organizations-for-selection-table', 'OrganizationsForSelectionTable');
    Route::get('/top-public-organizations', 'TopTenPublicOrganizations');
    Route::get('/public-organizations-with-selected-data', 'publishedOrganizationswithSelectedData');
    Route::get('/get-public-organizations-ids', 'getPublicOrganizationsIds');
    Route::get('/active-organizations', 'activeOrganizations');
    Route::get('/organizations-count', 'organizationsCount');
    Route::get('/organizations/{id}', 'show');
    Route::get('/organization-time-work/{id}', 'getOrgTimeWork');
    Route::post('/register-org', 'StoreOgranizationWithOffer');
    Route::post('/validate-org-email', 'validateEmail');
    Route::get('/organizations-locations', 'getLocations');
});



Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories-by-state', 'activeCategories');
    Route::get('/categories-with-subcategories', 'activeCategoriesWithSubCategories');
});


// ------------------------------------
//  Articles  Routes ------------
// ------------------------------------

Route::get('/top-ten-articles', [ArticleController::class, 'topTenArticlesByViews']);
Route::get('/last-three-articles', [ArticleController::class, 'getLastThree']);
Route::get('/articles-by-search', [ArticleController::class, 'getPublishedArticlesBySearch']);
Route::get('/articles-by-status/{status}', [ArticleController::class, 'getArticlesByStatus']);
Route::get('/articles-by-tag', [ArticleController::class, 'getArticlesByTag']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);
Route::get('/random-articles', [ArticleController::class, 'getRandomArticles']);


// ------------------------------------
//  Tags  Routes ------------
// ------------------------------------

Route::get('/tags', [TagController::class, 'index']);




// ------------------------------------
//  Privacy Policy  Routes ------------
// ------------------------------------

Route::get('/users-points', [PrivacyPolicyController::class, 'index']);

// ------------------------------------
//  organiztions  Routes ------------
// ------------------------------------

Route::get('/organizations-points', [OrganizationPrivacyPolicyController::class, 'index']);



// --------------------------------
//  TermsCondition Routes ------
// --------------------------------


Route::get('/users-terms', [TermsConditionController::class, 'index']);


// --------------------------------
//  TermsCondition Routes ------
// --------------------------------


Route::get('/organiztions-terms', [OrganizationTermsConditionController::class, 'index']);


// -------------------------
//  About Details Routes ---
// -------------------------

Route::get('/details', [AboutController::class, 'index']);
Route::get('/get-cooperation-file', [AboutController::class, 'getcooperation_pdf']);




// -------------------------
//  Auth Routes ------------
// -------------------------

Route::post('/login', [AuthController::class, 'login']);
Route::post('/send-otp', [AuthController::class, 'sendOTP']);
Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/register', [UserController::class, 'store']);



// -------------------------
//  google Routes ----------
// -------------------------

Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);



// ---------------------------------
// Contact  Messages Routes --------
// ---------------------------------

Route::post('/add-contact-message', [ContactMessageController::class, 'store']);

// ----------------------------------------
//  Questions Answers Routes --------------
// ----------------------------------------

Route::get('/approvedQuestions', [QuestionAnswerController::class, 'approvedQuestions']);




// -------------------------------
//  OrganizationReviews Routes ---
// -------------------------------

Route::controller(OrganizationReviewController::class)->group(function () {
    Route::get('/org-reviews/{id}', 'ReviewsForOrg');
    Route::get('/org-reviews-numbers/{id}', 'ReviewsNumbers');
});



// ----------------------------------------
// Subscribe to the newsletter ------------
// ----------------------------------------

Route::post('/subscribe', [MemberController::class, 'subscribe']);


Route::post('/send-sms', [SMSController::class, 'send']);


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// ------------------------------
// End public Routes ------------
// ------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


Route::get('/active-offers', [OfferController::class, 'activeOffers']);
Route::get('/active-offers/{orgId}', [OfferController::class, 'activeOffersByOrganization']);








Route::controller(AppointmentController::class)->group(function () {
    Route::get('/organizations/{organization}/available-times', 'getAvailableTimes');
    Route::get('/all-times/{organization}', 'getAllAppointments');
    Route::get('/organizations/{organization}/all-times', 'getAllTimes');
    Route::post('/organizations/{organization}/appointments',  'store');
    Route::post('/pending-appointments/{organization}',  'cancelAppointmentsByOwner');
    Route::post('/organizations/{organization}/appointments/{appointment}/response',  'respond');
    Route::get('/appointments/{type}/{id}', 'index');
    Route::post('/cancel-appointment', 'cancel');
    Route::delete('/delete-appointment', 'destroy');
});


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// -----------------------------------------
//  Start Protected Auth Routes ------------
// -----------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


Route::middleware(['auth:sanctum'])->group(function () {


    // -------------------------
    // Owned Cards Routes ----
    // -------------------------

    Route::get('/cards-account', [OwnedCardController::class, 'getAccountCards']);


    // -------------------------
    // Payment Routes ----
    // -------------------------

    Route::post('/payment/create-session', [PaymentController::class, 'createSession']);
    Route::post('/payment/webhook', [PaymentController::class, 'webhook']);
    Route::post('/payment/callback', [PaymentController::class, 'callback']);

    // -------------------------
    // WithdrawRequest Routes --
    // -------------------------


    Route::post('/wallet/withdraw', [WithdrawRequestController::class, 'withdraw']);



    // -------------------------
    // Promoter Routes --
    // -------------------------

    Route::get('/get-promoter/{id}', [PromoterController::class, 'getPromoter']);
    Route::post('/check-promoter-code', [PromoterController::class, 'checkPromoterCode']);
    Route::post('/promoter/track-visit', [PromoterTrackingController::class, 'trackVisit']);


    // ----------------------------
    // PromotionActivity Routes --
    // ----------------------------


    Route::get('/promoter-activities', [PromotionActivityController::class, 'getPromoterActivities']);


    // ----------------------------
    // service orders for user --
    // ----------------------------

    Route::get('/user-service-orders', [ServiceOrderController::class, 'userServiceOrders']);
    Route::get('/user-service-orders/{serviceOrder}', [ServiceOrderController::class, 'showOrder']);




    // -------------------------
    // family members Routes ---
    // -------------------------

    Route::controller(FamilyMemberController::class)->group(function () {
        Route::get('/family-members', 'index');
        Route::get('/family-members/pending', 'pendingRequests');
        Route::post('/add-family-member', 'store');
        Route::post('/family/{id}/accept', 'accept');
        Route::post('/family/{id}/reject', 'reject');
        Route::delete('/family/{id}', 'destroy');
    });



    // -------------------------
    //  Current-user Routes ----
    // -------------------------

    Route::controller(AuthController::class)->group(function () {
        Route::get('/current-user', 'getCurrentUser');
        Route::post('/logout', 'logout');
    });

    // -------------------------
    //  sms Routes -------------
    // -------------------------

    Route::post('/send-sms', [SMSController::class, 'send']);

    // -------------------------
    //  wallet-user Routes -----
    // -------------------------

    Route::get('/wallet', [WalletController::class, 'show']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/wallet/add-pending', [WalletController::class, 'addPending']);
    Route::post('/wallet/release-pending', [WalletController::class, 'releasePending']);
    Route::get('/user-transactions', [TransactionController::class, 'getUserTransactions']);





    // -------------------------
    //  Auth  users Routes -----
    // -------------------------

    Route::controller(UserController::class)->group(function () {
        Route::get('/users-for-center', 'usersForCenter');
        Route::get('/user/{id}', 'show');
        Route::post('/update-user/{id}', 'update');
        Route::post('/check-password-user/{id}', 'checkPassword');
    });


    // ----------------------------------------
    //  Organizations Routes ------------------
    // ----------------------------------------


    Route::controller(OrganizationController::class)->group(function () {
        Route::post('/add-organization', 'store');
        Route::post('/update-organization/{id}', 'update');
        Route::post('/ckeck-organization-password/{id}', 'checkOrgPassword');
        Route::delete('/delete-organization/{id}', 'destroy');
    });


    // -------------------------------
    //  OrganizationReviews Routes ---
    // -------------------------------
    Route::controller(OrganizationReviewController::class)->group(function () {
        Route::post('/add-review', 'store');
        Route::delete('/delete-review/{id}', 'destroy');
    });



    // -----------------------------
    //  Notifications   Routes -----
    // -----------------------------

    Route::controller(NotificationController::class)->group(function () {
        Route::post('/send-notification', 'sendNotification');
        Route::get('/notifications/{id}/{type}', 'getNotificationsForAccount');
        Route::get('/last-ten-notifications/{id}/{type}', 'getLastTenNotifications');
        Route::post('/make-notifications-readed/{id}', 'makeAllNotificationsAsRead');
    });


    // ------------------------------------------
    //  Article Interactions Routes -------------
    // ------------------------------------------

    Route::post('/check-user-interaction', [ArticleInteractionsController::class, 'checkUserInteraction']);
    Route::post('/add-article-interaction', [ArticleInteractionsController::class, 'addInterAction']);
    Route::post('/update-article-interaction', [ArticleInteractionsController::class, 'updateInteraction']);
    Route::delete('/cancel-article-interaction', [ArticleInteractionsController::class, 'removeInteraction']);


    // ----------------------------------------
    //  Coupons Routes ------------
    // ----------------------------------------


    Route::get('/account-coupons', [CouponController::class, 'accountCoupons']);
    Route::post('/check-coupon', [CouponController::class, 'checkCoupon']);
    Route::post('/distribute-coupon', [CouponController::class, 'distribute']);
    Route::get('/get-coupon/{id}', [CouponController::class, 'show']);


    // ----------------------------------------
    //  Coupons Routes ------------
    // ----------------------------------------


    Route::get('/account-offers', [OfferController::class, 'accountOffers']);
    Route::post('add-offer', [OfferController::class, 'store']);
    Route::get('/get-offer/{id}', [OfferController::class, 'show']);

    // ------------------------------------------
    //  Article Comments Routes -----------------
    // ------------------------------------------

    Route::controller(ArticleCommentController::class)->group(function () {
        Route::get('/article-comments', 'ArticleComments');
        Route::post('/add-comment', 'store');
        Route::post('/update-comment/{id}', 'updateComment');
        Route::post('/like-comment/{id}', 'likeComment');
        Route::post('/unlike-comment/{id}', 'unlikeComment');
    });




    // ------------------------------------------
    //  Conversations  Routes -------------------
    // ------------------------------------------

    Route::controller(ConversationController::class)->group(function () {
        Route::post('/start-conversation',  'StoreConversation');
        Route::get('/conversation/show',  'getConversation');
        Route::post('/active-conversation', 'setActiveConversation');
        Route::post('/clear-active-conversation', 'clearActiveConversation');
        Route::get('/user/{id}/conversations',  'getUserConversations');
        Route::post('/conversations/{conversationId}/block',  'blockUser');
        Route::delete('/conversations/{conversationId}/unblock',  'unblockUser');
    });



    // ------------------------------------------
    //  Messages  Routes ------------------------
    // ------------------------------------------

    Route::controller(MessageController::class)->group(function () {
        Route::post('/send-message', 'store');
        Route::delete('/messages/{messageId}',  'destroy');
        Route::post('/conversation/mark-as-read',  'markAsRead');
    });


    // ------------------------------------------
    //  Service Tracking Routes (User) ----------
    // ------------------------------------------

    Route::controller(ServiceTrackingController::class)->group(function () {
        Route::get('/my-service-trackings', 'myTrackings');
        Route::get('/my-service-trackings/{serviceTracking}', 'myTrackingShow');
        Route::get('/my-active-trackings-count', 'myActiveCount');
        Route::post('/cancel-my-tracking/{serviceTracking}', 'cancelMyTracking');
        Route::get('/tracking-by-order/{orderId}', 'getByOrder');
        Route::get('/tracking-by-invoice/{invoiceId}', 'getByInvoice');
    });


    // ------------------------------------------
    //  Service Form Submission Routes (User) ---
    // ------------------------------------------

    Route::controller(ServiceFormSubmissionController::class)->group(function () {
        Route::post('/submit-service-form/{serviceForm}', 'submit');
        Route::get('/my-form-submissions', 'mySubmissions');
        Route::get('/my-form-submission/{submission}', 'mySubmissionShow');
    });


    // ------------------------------------------
    //  Temp File Upload Routes ------------------
    // ------------------------------------------

    Route::controller(TempUploadController::class)->group(function () {
        Route::post('/uploads/temp', 'upload');
        Route::delete('/uploads/temp/{uuid}', 'destroy');
    });
});


//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// -----------------------------------------
//  End Protected Auth Routes ------------
// -----------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------






//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// -------------------------------
// start  Admin  Routes ----------
// -------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------





/////////////////////////////////////////
// Check reviews Likes Routes ///////////
/////////////////////////////////////////

Route::post('/react-review', [ReviewLikesCheckController::class, 'store']);
Route::get('/review-like-user/{orgId}/{userId}', [ReviewLikesCheckController::class, 'GetReviewsForUser']);
Route::delete('/review-like/{reviewId}/{userId}', [ReviewLikesCheckController::class, 'destroy']);



Route::middleware(['auth:sanctum', 'checkAdmin'])->group(function () {


    // ----------------------------
    //  service orders for admin --
    // ----------------------------

    Route::get('/get-service-orders-options', [ServiceOrderController::class, 'getFilterOptions']);
    Route::get('/all-service-orders', [ServiceOrderController::class, 'index']);
    Route::get('/service-orders/{serviceOrder}', [ServiceOrderController::class, 'adminShow']);
    Route::post('/service-orders/{serviceOrder}/update-status', [ServiceOrderController::class, 'updateStatus']);


    // ----------------------------------------
    //  Variable Data Routes ----------------
    // ----------------------------------------

    Route::post('/update-variable-data', [VariableDataController::class, 'updateVariablesData']);


    // ----------------------------------------
    //  Dashboard Main Page Routes ------------
    // ----------------------------------------

    Route::get('/dashboard-main-page-stats', [DashboardMainPageController::class, 'getStats']);
    Route::get('/charts-data', [DashboardMainPageController::class, 'getChartsData']);


    // --------------------------------
    //  Website Video Routes ----
    // --------------------------------

    Route::controller(WebsiteVideoController::class)->group(function () {
        Route::post('/update-video', 'updateVideo');
    });


    // ----------------------------------------
    //  Promoter Ratio Routes ----------------
    // ----------------------------------------

    Route::get('/get-ratios', [PromoterRatioController::class, 'getRatiosRatio']);
    Route::put('/update-ratios', [PromoterRatioController::class, 'updateRatiosRatio']);



    // ----------------------------------------
    //  home page Routes ----------------
    // ----------------------------------------

    Route::post('/update-section/{id}', [HomePageController::class, 'updateSection']);


    // ----------------------------------------
    // Promoters Routes ----------------
    // ----------------------------------------

    Route::get('/promoter-data', [PromotionActivityController::class, 'getPromoterData']);
    Route::get('/top-promoters-data', [PromotionActivityController::class, 'getTopPromotersData']);
    Route::get('/promoter-activities-by-type', [PromotionActivityController::class, 'getActivitiesByType']);
    Route::get('/top-referred-buyers', [PromotionActivityController::class, 'getTopReferredBuyers']);


    // ----------------------------------------
    //  Slides Routes ----------------
    // ----------------------------------------


    Route::controller(SlideController::class)->group(function () {
        Route::get('/slides', 'index');
        Route::post('/add-slide', 'store');
        Route::get('/get-slide/{id}', 'show');
        Route::post('/update-slide/{id}', 'update');
        Route::delete('/delete-slide/{id}', 'destroy');
    });

    Route::controller(NotificationController::class)->group(function () {
        Route::post('/send-multiple-notification', 'sendMultipleNotification');
    });

    // -------------------------
    // users Routes ------------
    // -------------------------

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::get('/users-with-selected-data', 'usersWithSelectedData');
        Route::get('/get-public-users-ids', 'getPublicUsersIds');
        Route::get('/users-ids', 'getUsersIds');
        Route::get('/users-count', 'getUsersCount');
        Route::post('/search-for-user-by-name', 'searchForUsers');
        Route::delete('/delete-user/{id}', 'destroy');
    });


    // -------------------------
    // promoters Routes ------------
    // -------------------------

    Route::controller(PromoterController::class)->group(function () {
        Route::get('/promoters', 'index');
        Route::post('/search-for-promoter', 'searchForPromoters');
        Route::post('/add-promoter', 'addPromoter');
        Route::post('/update-promoter/{promoter}', 'updatePromoter');
        Route::delete('/delete-promoter/{id}', 'deletePromoter');
    });


    // ----------------------------------------
    //  Cards Routes --------------------------
    // ----------------------------------------


    Route::controller(CardController::class)->group(function () {
        Route::get('/dashboard/cards', 'index');
        Route::post('/dashboard/add-card', 'store');
        Route::post('/dashboard/update-card/{id}', 'update');
        Route::delete('/dashboard/delete-card/{id}', 'destroy');
    });



    // ----------------------------------------
    //  Serivce Pages Routes ------------------
    // ----------------------------------------
    Route::controller(ServicePageController::class)->group(function () {
        Route::get('/dashboard/service-pages', 'adminIndex');
        Route::get('/dashboard/service-pages/{id}', 'adminShow');
        Route::post('/dashboard/service-pages', 'store');
        Route::post('/dashboard/service-pages/{id}', 'update');
        Route::delete('/dashboard/service-pages/{id}', 'destroy');
    });


    // ----------------------------------------
    //  service page contact messages Routes ---
    // ----------------------------------------

    Route::controller(ServicePageContactMessageController::class)->group(function () {
        Route::post('/update-service-message/{message}', 'update');
        Route::delete('/delete-service-message/{message}', 'destroy');
    });



    // ----------------------------------------
    //  Organizations Routes ------------------
    // ----------------------------------------


    Route::controller(OrganizationController::class)->group(function () {
        Route::get('/dashboard/organizations', 'index');
        Route::get('/dashboard/organizations-table', 'OrganizationsForSelectionTable');
        Route::get('/dashboard/organizations-ids', 'getOrganizationsIds');
        Route::get('/dashboard/organizations-with-selected-data', 'organizationWithSelectedData');
    });



    // ----------------------------------------
    //  Coupons Routes ------------
    // ----------------------------------------

    Route::controller(CouponController::class)->group(function () {
        Route::get('/dashboard/coupons',  'index');
        Route::get('/dashboard/active-coupons',  'activeCoupons');
        Route::post('/dashboard/add-coupon', 'store');
        Route::post('/dashboard/send-coupon', 'sendCoupon');
        Route::post('/dashboard/update-coupon/{id}', 'update');
        Route::delete('/dashboard/delete-coupon/{id}', 'destroy');
    });




    // ----------------------------------------
    //  Offers Routes ------------
    // ----------------------------------------

    Route::controller(OfferController::class)->group(function () {
        Route::get('/dashboard/offers',  'index');
        Route::post('/dashboard/update-offer/{id}', 'update');
        Route::post('/dashboard/update-status-offer/{id}', 'updateStatus');
        Route::delete('/dashboard/delete-offer/{id}', 'destroy');
    });


    // -------------------------------------
    // WithdrawRequest Routes --------------
    // -------------------------------------

    Route::post('/admin/withdraw-requests/{id}/approve', [WithdrawRequestController::class, 'approve']);
    Route::post('/admin/withdraw-requests/{id}/reject', [WithdrawRequestController::class, 'reject']);
    Route::get('/withdraw-requests', [WithdrawRequestController::class, 'index']);
    Route::get('/withdraw-requests/{id}', [WithdrawRequestController::class, 'show']);



    // ---------------------------------------
    // main Categories Routes ------------
    // ---------------------------------------

    Route::controller(CategoryController::class)->group(function () {
        Route::post('/add-category', 'store');
        Route::post('/categories/search', 'search');
        Route::get('/category/{id}', 'show');
        Route::post('/update-category/{id}', 'update');
        Route::post('/update-category-state/{id}', 'updateState');
        Route::delete('/delete-category/{id}', 'destroy');
    });


    // ---------------------------------------
    // main Sub Categories Routes ------------
    // ---------------------------------------

    Route::controller(SubCategoryController::class)->group(function () {
        Route::post('/add-sub-category', 'store');
        Route::post('/sub-categories/search', 'search');
        Route::get('/sub-categories-by-state', 'activeSubCategories');
        Route::get('/sub-category/{id}', 'show');
        Route::post('/update-sub-category/{id}', 'update');
        Route::post('/update-sub-category-state/{id}', 'updateState');
        Route::delete('/delete-sub-category/{id}', 'destroy');
    });



    // ---------------------------------------
    // main Categories Routes ------------
    // ---------------------------------------

    Route::controller(ServiceCategoryController::class)->group(function () {
        Route::post('/add-service-category', 'store');
        Route::post('/service-categories/search', 'search');
        Route::get('/service-category/{id}', 'show');
        Route::post('/update-service-category/{id}', 'update');
        Route::post('/update-service-category-state/{id}', 'updateState');
        Route::delete('/delete-service-category/{id}', 'destroy');
    });


    // ---------------------------------------
    // Card Categories Routes ----------------
    // ---------------------------------------

    Route::controller(CardCategoryController::class)->group(function () {
        Route::post('/add-card-category', 'store');
        Route::post('/card-categories/search', 'search');
        Route::get('/card-categories-by-state', 'activeCategories');
        Route::get('/card-category/{id}', 'show');
        Route::post('/update-card-category/{id}', 'update');
        Route::post('/update-card-category-state/{id}', 'updateState');
        Route::delete('/delete-card-category/{id}', 'destroy');
    });


    // ---------------------------------------
    // Articles Categories Routes ------------
    // ---------------------------------------

    Route::controller(ArticleCategoryController::class)->group(function () {
        Route::post('/add-article-category', 'store');
        Route::get('/article-category/{id}', 'show');
        Route::post('/update-article-category/{id}', 'update');
        Route::delete('/delete-article-category/{id}', 'destroy');
        Route::post('/article-categories/search', 'search');
    });



    // ---------------------------------------
    // Currencies  Routes --------------------
    // ---------------------------------------

    Route::controller(CurrencyController::class)->group(function () {
        Route::post('/dashboard/add-currency', 'store');
        Route::get('/dashboard/currencies/{currencyId}', 'show');
        Route::post('/dashboard/update-currency/{currencyId}', 'update');
        Route::delete('/dashboard/currencies/{currencyId}', 'destroy');
    });


    // ---------------------------------------
    // Articles  Routes ----------------------
    // ---------------------------------------

    Route::controller(ArticleController::class)->group(function () {
        Route::get('/articles', 'index');
        Route::post('/get-articles-by-search', 'getArticlesBySearch');
        Route::post('/add-article', 'store');
        Route::post('/update-article/{id}', 'update');
        Route::delete('/delete-article/{id}', 'destroy');
    });




    // ---------------------------------
    // Contact  Messages Routes --------
    // ---------------------------------

    Route::controller(ContactMessageController::class)->group(function () {
        Route::get('/contact-messages', 'index');
        Route::get('/contact-message/{id}', 'show');
        Route::post('/update-contact-message/{id}', 'update');
        Route::delete('/contact-message/{id}', 'destroy');
    });


    // ---------------------------------
    // Questions Answers Routes --------
    // ---------------------------------

    Route::controller(QuestionAnswerController::class)->group(function () {
        Route::get('/all-faqs', 'index');
        Route::post('/add-faq', 'store');
        Route::get('/get-faq/{id}', 'show');
        Route::post('/update-faq/{id}', 'update');
        Route::delete('/delete-faq/{id}', 'destroy');
    });

    // ---------------------------------
    // Footer  Links Routes ------------
    // ---------------------------------


    Route::controller(FooterLinkController::class)->group(function () {
        Route::post('/add-link', 'store');
        Route::get('/get-link/{id}', 'show');
        Route::post('/update-link/{id}', 'update');
        Route::delete('/delete-link/{id}', 'destroy');
    });


    // ---------------------------------
    // privacy policy Routes -----------
    // ---------------------------------


    Route::controller(PrivacyPolicyController::class)->group(function () {
        Route::post('/add-user-point', 'store');
        Route::post('/update-user-point/{id}', 'update');
        Route::delete('/user-point/{id}', 'destroy');
    });


    // -----------------------------------------------
    // organizations privacy policy Routes -----------
    // -----------------------------------------------


    Route::controller(OrganizationPrivacyPolicyController::class)->group(function () {
        Route::post('/add-organization-point', 'store');
        Route::post('/update-organization-point/{id}', 'update');
        Route::delete('/organization-point/{id}', 'destroy');
    });



    // --------------------------------
    //  TermsCondition Routes ------
    // --------------------------------

    Route::controller(TermsConditionController::class)->group(function () {
        Route::post('/add-user-term', 'store');
        Route::post('/update-user-term/{id}', 'update');
        Route::delete('/user-term/{id}', 'destroy');
    });


    // -----------------------------------------
    // organiztions TermsCondition Routes ------
    // -----------------------------------------

    Route::controller(TermsConditionController::class)->group(function () {
        Route::post('/add-oranization-term', 'store');
        Route::post('/update-oranization-term/{id}', 'update');
        Route::delete('/oranization-term/{id}', 'destroy');
    });




    // --------------------------------
    //  Social Contact Info Routes ----
    // --------------------------------


    Route::controller(SocialAccountController::class)->group(function () {
        Route::post('/update-social-contact-info', 'update');
    });




    // -------------------------
    //  News Letter Routes -----
    // -------------------------

    Route::controller(MemberController::class)->group(function () {
        Route::get('/members',  'index');
        Route::post('/members-by-email/{searchContent}',  'getMembersByEmail');
        Route::get('/get-members-ids',  'getMembersIds');
        Route::post('/send-newsletter',  'sendNewsletter');
        Route::delete('/unsubscribe/{id}',  'unsubscribe');
    });



    // -------------------------
    //  About Details Routes ---
    // -------------------------

    Route::post('/update-details', [AboutController::class, 'update']);
    Route::post('/update-uploadcooperation-file', [AboutController::class, 'uploadcooperation_pdf']);
    // Route::post('/details', [AboutController::class, 'index']);


    // -------------------------
    //  keywords Routes ---
    // -------------------------

    Route::post('/add-keyword', [KeywordController::class, 'store']);
    Route::post('/delete-keyword', [KeywordController::class, 'destroy']);


    // ------------------------------------------
    //  Service Tracking Routes (Admin) ---------
    // ------------------------------------------

    Route::controller(ServiceTrackingController::class)->group(function () {
        // CRUD Routes
        Route::get('/service-trackings', 'index');
        Route::post('/add-service-tracking', 'store');
        Route::get('/service-tracking/{serviceTracking}', 'show');
        Route::post('/update-service-tracking/{serviceTracking}', 'update');
        Route::delete('/delete-service-tracking/{serviceTracking}', 'destroy');

        // Status and Phase Management
        Route::post('/service-tracking/{serviceTracking}/status', 'updateStatus');
        Route::post('/service-tracking/{serviceTracking}/phase', 'updatePhase');
        Route::post('/service-tracking/{serviceTracking}/advance-phase', 'advancePhase');

        // Statistics and Options
        Route::get('/service-tracking-statistics', 'statistics');
        Route::get('/service-tracking-options', 'getOptions');
    });



    // ------------------------------------------
    //  Service Orders Routes (Admin) ------------
    // ------------------------------------------

    Route::controller(ServiceOrderController::class)->group(function () {
        Route::get('/service-orders', 'index');
        Route::post('/add-service-order', 'store');
        Route::get('/service-order/{serviceOrder}', 'show');
        Route::post('/update-service-order/{serviceOrder}', 'update');
        Route::delete('/delete-service-order/{serviceOrder}', 'destroy');
    });


    // ------------------------------------------
    //  Service Forms Routes (Admin) ------------
    // ------------------------------------------

    Route::controller(ServiceFormController::class)->group(function () {
        Route::get('/service-forms', 'index');
        Route::post('/add-service-form', 'store');
        Route::get('/service-form/{serviceForm}', 'show');
        Route::post('/update-service-form/{serviceForm}', 'update');
        Route::delete('/delete-service-form/{serviceForm}', 'destroy');
        Route::post('/duplicate-service-form/{serviceForm}', 'duplicate');
        Route::post('/toggle-service-form/{serviceForm}', 'toggleActive');
    });


    // ------------------------------------------
    //  Service Form Fields Routes (Admin) ------
    // ------------------------------------------

    Route::controller(ServiceFormFieldController::class)->group(function () {
        Route::get('/service-form/{serviceForm}/fields', 'index');
        Route::post('/service-form/{serviceForm}/fields', 'store');
        Route::get('/service-form-field/{serviceFormField}', 'show');
        Route::post('/update-service-form-field/{serviceFormField}', 'update');
        Route::delete('/delete-service-form-field/{serviceFormField}', 'destroy');
        Route::post('/reorder-service-form-fields/{serviceForm}', 'reorder');
        Route::get('/service-form-field-types', 'getFieldTypes');
    });


    // ------------------------------------------
    //  Service Form Submissions (Admin) --------
    // ------------------------------------------

    Route::controller(ServiceFormSubmissionController::class)->group(function () {
        Route::get('/service-form-submissions', 'index');
        Route::get('/service-form-submission/{submission}', 'show');
        Route::post('/service-form-submission/{submission}/status', 'updateStatus');
        Route::delete('/delete-service-form-submission/{submission}', 'destroy');
        Route::get('/service-form-submission-statistics', 'statistics');
    });
});


    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // -------------------------------
    // End  Admin  Routes ----------
    // -------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
