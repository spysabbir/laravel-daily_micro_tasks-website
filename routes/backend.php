<?php

use App\Http\Controllers\Backend\BackendController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ChildCategoryController;
use App\Http\Controllers\Backend\DepositController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\ExpenseCategoryController;
use App\Http\Controllers\Backend\ExpenseController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\TaskController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\RolePermissionController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SubCategoryController;
use App\Http\Controllers\Backend\SubscriberController;
use App\Http\Controllers\Backend\TaskPostChargeController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\VerificationController;
use App\Http\Controllers\Backend\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::prefix('backend')->name('backend.')->middleware(['check_user_type:Backend'])->group(function() {
    Route::get('dashboard', [BackendController::class, 'dashboard'])->name('dashboard');
    Route::get('profile/edit', [BackendController::class, 'profileEdit'])->name('profile.edit');
    Route::get('profile/setting', [BackendController::class, 'profileSetting'])->name('profile.setting');
    // Role & Permission
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('role-permission', RolePermissionController::class);
    // Setting
    Route::get('site/setting', [SettingController::class, 'siteSetting'])->name('site.setting');
    Route::post('site/setting/update', [SettingController::class, 'siteSettingUpdate'])->name('site.setting.update');
    Route::get('default/setting', [SettingController::class, 'defaultSetting'])->name('default.setting');
    Route::post('default/setting/update', [SettingController::class, 'defaultSettingUpdate'])->name('default.setting.update');
    Route::get('seo/setting', [SettingController::class, 'seoSetting'])->name('seo.setting');
    Route::post('seo/setting/update', [SettingController::class, 'seoSettingUpdate'])->name('seo.setting.update');
    Route::get('mail/setting', [SettingController::class, 'mailSetting'])->name('mail.setting');
    Route::post('mail/setting/update', [SettingController::class, 'mailSettingUpdate'])->name('mail.setting.update');
    Route::get('sms/setting', [SettingController::class, 'smsSetting'])->name('sms.setting');
    Route::post('sms/setting/update', [SettingController::class, 'smsSettingUpdate'])->name('sms.setting.update');
    Route::get('captcha/setting', [SettingController::class, 'captchaSetting'])->name('captcha.setting');
    Route::post('captcha/setting/update', [SettingController::class, 'captchaSettingUpdate'])->name('captcha.setting.update');
    // Expense Category
    Route::resource('expense_category', ExpenseCategoryController::class);
    Route::get('expense_category-trash', [ExpenseCategoryController::class, 'trash'])->name('expense_category.trash');
    Route::get('expense_category/restore/{id}', [ExpenseCategoryController::class, 'restore'])->name('expense_category.restore');
    Route::get('expense_category/delete/{id}', [ExpenseCategoryController::class, 'delete'])->name('expense_category.delete');
    Route::get('expense_category/status/{id}', [ExpenseCategoryController::class, 'status'])->name('expense_category.status');
    // Expense
    Route::resource('expense', ExpenseController::class);
    Route::get('expense-trash', [ExpenseController::class, 'trash'])->name('expense.trash');
    Route::get('expense/restore/{id}', [ExpenseController::class, 'restore'])->name('expense.restore');
    Route::get('expense/delete/{id}', [ExpenseController::class, 'delete'])->name('expense.delete');
    Route::get('expense/status/{id}', [ExpenseController::class, 'status'])->name('expense.status');
    // Report
    Route::get('report/deposit', [ReportController::class, 'depositReport'])->name('deposit.report');
    Route::get('report/withdraw', [ReportController::class, 'withdrawReport'])->name('withdraw.report');
    Route::get('report/bonus', [ReportController::class, 'bonusReport'])->name('bonus.report');
    Route::get('report/expense', [ReportController::class, 'expenseReport'])->name('expense.report');
    Route::get('report/posted/task', [ReportController::class, 'postedTaskReport'])->name('posted_task.report');
    Route::get('report/worked/task', [ReportController::class, 'workedTaskReport'])->name('worked_task.report');
    // Employee
    Route::resource('employee', EmployeeController::class);
    Route::get('employee-inactive', [EmployeeController::class, 'inactive'])->name('employee.inactive');
    Route::get('employee-trash', [EmployeeController::class, 'trash'])->name('employee.trash');
    Route::get('employee/restore/{id}', [EmployeeController::class, 'restore'])->name('employee.restore');
    Route::get('employee/delete/{id}', [EmployeeController::class, 'delete'])->name('employee.delete');
    Route::get('employee/status/{id}', [EmployeeController::class, 'status'])->name('employee.status');
    // User
    Route::get('user/active', [BackendController::class, 'userActiveList'])->name('user.active');
    Route::get('user/show/{id}', [BackendController::class, 'userView'])->name('user.show');
    Route::get('user/status/{id}', [BackendController::class, 'userStatus'])->name('user.status');
    Route::post('user/status/update/{id}', [BackendController::class, 'userStatusUpdate'])->name('user.status.update');
    Route::delete('user/destroy/{id}', [BackendController::class, 'userDestroy'])->name('user.destroy');
    Route::get('user/trash', [BackendController::class, 'userTrash'])->name('user.trash');
    Route::get('user/restore/{id}', [BackendController::class, 'userRestore'])->name('user.restore');
    Route::get('user/delete/{id}', [BackendController::class, 'userDelete'])->name('user.delete');
    Route::get('user/inactive', [BackendController::class, 'userInactiveList'])->name('user.inactive');
    Route::get('user/blocked', [BackendController::class, 'userBlockedList'])->name('user.blocked');
    Route::get('user/banned', [BackendController::class, 'userBannedList'])->name('user.banned');
    // Category
    Route::resource('category', CategoryController::class);
    Route::get('category-trash', [CategoryController::class, 'trash'])->name('category.trash');
    Route::get('category/restore/{id}', [CategoryController::class, 'restore'])->name('category.restore');
    Route::get('category/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');
    Route::get('category/status/{id}', [CategoryController::class, 'status'])->name('category.status');
    // Sub Category
    Route::resource('sub_category', SubCategoryController::class);
    Route::get('sub_category-trash', [SubCategoryController::class, 'trash'])->name('sub_category.trash');
    Route::get('sub_category/restore/{id}', [SubCategoryController::class, 'restore'])->name('sub_category.restore');
    Route::get('sub_category/delete/{id}', [SubCategoryController::class, 'delete'])->name('sub_category.delete');
    Route::get('sub_category/status/{id}', [SubCategoryController::class, 'status'])->name('sub_category.status');
    // Child Category
    Route::resource('child_category', ChildCategoryController::class);
    Route::get('child_category-get_sub_category', [ChildCategoryController::class, 'getSubCategories'])->name('child_category.get_sub_categories');
    Route::get('child_category-trash', [ChildCategoryController::class, 'trash'])->name('child_category.trash');
    Route::get('child_category/restore/{id}', [ChildCategoryController::class, 'restore'])->name('child_category.restore');
    Route::get('child_category/delete/{id}', [ChildCategoryController::class, 'delete'])->name('child_category.delete');
    Route::get('child_category/status/{id}', [ChildCategoryController::class, 'status'])->name('child_category.status');
    // Task Post Charge
    Route::resource('task_post_charge', TaskPostChargeController::class);
    Route::get('task_post_charge-get_sub_category', [TaskPostChargeController::class, 'getSubCategories'])->name('task_post_charge.get_sub_categories');
    Route::get('task_post_charge-get_child_category', [TaskPostChargeController::class, 'getChildCategories'])->name('task_post_charge.get_child_categories');
    Route::get('task_post_charge-trash', [TaskPostChargeController::class, 'trash'])->name('task_post_charge.trash');
    Route::get('task_post_charge/restore/{id}', [TaskPostChargeController::class, 'restore'])->name('task_post_charge.restore');
    Route::get('task_post_charge/delete/{id}', [TaskPostChargeController::class, 'delete'])->name('task_post_charge.delete');
    Route::get('task_post_charge/status/{id}', [TaskPostChargeController::class, 'status'])->name('task_post_charge.status');
    // Faq
    Route::resource('faq', FaqController::class);
    Route::get('faq-trash', [FaqController::class, 'trash'])->name('faq.trash');
    Route::get('faq/restore/{id}', [FaqController::class, 'restore'])->name('faq.restore');
    Route::get('faq/delete/{id}', [FaqController::class, 'delete'])->name('faq.delete');
    Route::get('faq/status/{id}', [FaqController::class, 'status'])->name('faq.status');
    // Testimonial
    Route::resource('testimonial', TestimonialController::class);
    Route::get('testimonial-trash', [TestimonialController::class, 'trash'])->name('testimonial.trash');
    Route::get('testimonial/restore/{id}', [TestimonialController::class, 'restore'])->name('testimonial.restore');
    Route::get('testimonial/delete/{id}', [TestimonialController::class, 'delete'])->name('testimonial.delete');
    Route::get('testimonial/status/{id}', [TestimonialController::class, 'status'])->name('testimonial.status');
    // Verification
    Route::get('verification-request', [VerificationController::class, 'verificationRequest'])->name('verification.request');
    Route::get('verification-request/{id}', [VerificationController::class, 'verificationRequestShow'])->name('verification.request.show');
    Route::put('verification-request-status-change/{id}', [VerificationController::class, 'verificationRequestStatusChange'])->name('verification.request.status.change');
    Route::get('verification-request-rejected', [VerificationController::class, 'verificationRequestRejected'])->name('verification.request.rejected');
    Route::get('verification-request-approved', [VerificationController::class, 'verificationRequestApproved'])->name('verification.request.approved');
    Route::delete('verification-request-delete/{id}', [VerificationController::class, 'verificationRequestDelete'])->name('verification.request.delete');
    // Deposit
    Route::get('deposit-request', [DepositController::class, 'depositRequest'])->name('deposit.request');
    Route::get('deposit-request/{id}', [DepositController::class, 'depositRequestShow'])->name('deposit.request.show');
    Route::put('deposit-request-status-change/{id}', [DepositController::class, 'depositRequestStatusChange'])->name('deposit.request.status.change');
    Route::get('deposit-request-rejected', [DepositController::class, 'depositRequestRejected'])->name('deposit.request.rejected');
    Route::get('deposit-request-approved', [DepositController::class, 'depositRequestApproved'])->name('deposit.request.approved');
    Route::delete('deposit-request-delete/{id}', [DepositController::class, 'depositRequestDelete'])->name('deposit.request.delete');
    // Withdraw
    Route::get('withdraw-request', [WithdrawController::class, 'withdrawRequest'])->name('withdraw.request');
    Route::get('withdraw-request/{id}', [WithdrawController::class, 'withdrawRequestShow'])->name('withdraw.request.show');
    Route::put('withdraw-request-status-change/{id}', [WithdrawController::class, 'withdrawRequestStatusChange'])->name('withdraw.request.status.change');
    Route::get('withdraw-request-rejected', [WithdrawController::class, 'withdrawRequestRejected'])->name('withdraw.request.rejected');
    Route::get('withdraw-request-approved', [WithdrawController::class, 'withdrawRequestApproved'])->name('withdraw.request.approved');
    Route::delete('withdraw-request-delete/{id}', [WithdrawController::class, 'withdrawRequestDelete'])->name('withdraw.request.delete');
    // Posted Task
    Route::get('posted_task_list-pending', [TaskController::class, 'postedTaskListPending'])->name('posted_task_list.pending');
    Route::get('pending-posted_task_view/{id}', [TaskController::class, 'pendingPostedTaskView'])->name('pending.posted_task_view');
    Route::get('posted_task_list-running', [TaskController::class, 'postedTaskListRunning'])->name('posted_task_list.running');
    Route::get('running-posted_task_view/{id}', [TaskController::class, 'runningPostedTaskView'])->name('running.posted_task_view');
    Route::get('posted_task_list-rejected', [TaskController::class, 'postedTaskListRejected'])->name('posted_task_list.rejected');
    Route::get('rejected-posted_task_view/{id}', [TaskController::class, 'rejectedPostedTaskView'])->name('rejected.posted_task_view');
    Route::get('posted_task_list-canceled', [TaskController::class, 'postedTaskListCanceled'])->name('posted_task_list.canceled');
    Route::get('canceled-posted_task_view/{id}', [TaskController::class, 'canceledPostedTaskView'])->name('canceled.posted_task_view');
    Route::get('posted_task_list-completed', [TaskController::class, 'postedTaskListCompleted'])->name('posted_task_list.completed');
    Route::get('completed-posted_task_view/{id}', [TaskController::class, 'completedPostedTaskView'])->name('completed.posted_task_view');

    Route::put('posted_task_status_update/{id}', [TaskController::class, 'postedTaskStatusUpdate'])->name('posted_task_status_update');
    // Worked  Task
    Route::get('worked_task_list-pending', [TaskController::class, 'workedTaskListPending'])->name('worked_task_list.pending');
    Route::get('pending-worked_task_view/{id}', [TaskController::class, 'pendingWorkedTaskView'])->name('pending.worked_task_view');
    Route::get('worked_task_list-approved', [TaskController::class, 'workedTaskListApproved'])->name('worked_task_list.approved');
    Route::get('approved-worked_task_view/{id}', [TaskController::class, 'approvedWorkedTaskView'])->name('approved.worked_task_view');
    Route::get('worked_task_list-rejected', [TaskController::class, 'workedTaskListRejected'])->name('worked_task_list.rejected');
    Route::get('rejected-worked_task_view/{id}', [TaskController::class, 'rejectedWorkedTaskView'])->name('rejected.worked_task_view');
    Route::get('worked_task_list-reviewed', [TaskController::class, 'workedTaskListReviewed'])->name('worked_task_list.reviewed');
    Route::get('reviewed-worked_task_view/{id}', [TaskController::class, 'reviewedWorkedTaskView'])->name('reviewed.worked_task_view');

    Route::get('worked_task_check/{id}', [TaskController::class, 'workedTaskCheck'])->name('worked_task_check');
    Route::put('worked_task_check_update/{id}', [TaskController::class, 'workedTaskCheckUpdate'])->name('worked_task_check_update');
    // Report User
    Route::get('report_user-pending', [BackendController::class, 'reportUserPending'])->name('report_user.pending');
    Route::get('report_user-resolved', [BackendController::class, 'reportUserResolved'])->name('report_user.resolved');
    Route::get('report_user-view/{id}', [BackendController::class, 'reportUserView'])->name('report_user.view');
    Route::post('report_user-reply', [BackendController::class, 'reportUserReply'])->name('report_user.reply');
    // Support
    Route::get('support', [BackendController::class, 'support'])->name('support');
    Route::get('/get/support/users/{userId}', [BackendController::class, 'getSupportUsers'])->name('get.support.users');
    Route::get('/get/latest/support/users', [BackendController::class, 'getLatestSupportUsers'])->name('get.latest.support.users');
    Route::post('/support-send-message-reply/{userId}', [BackendController::class, 'supportSendMessageReply'])->name('support.send-message.reply');
    // Contact
    Route::get('contact', [BackendController::class, 'contact'])->name('contact');
    Route::get('contact-view/{id}', [BackendController::class, 'contactView'])->name('contact.view');
    Route::get('contact-delete/{id}', [BackendController::class, 'contactDelete'])->name('contact.delete');
    // Subscriber
    Route::get('subscriber', [SubscriberController::class, 'subscriber'])->name('subscriber.index');
    Route::get('subscriber-delete/{id}', [SubscriberController::class, 'subscriberDelete'])->name('subscriber.delete');
    Route::get('subscriber-newsletter', [SubscriberController::class, 'subscriberNewsletter'])->name('subscriber.newsletter');
    Route::post('subscriber-newsletter-send', [SubscriberController::class, 'subscriberNewsletterSend'])->name('subscriber.newsletter.send');
    Route::get('subscriber-newsletter-view/{id}', [SubscriberController::class, 'subscriberNewsletterView'])->name('subscriber.newsletter.view');
    Route::get('subscriber-newsletter-delete/{id}', [SubscriberController::class, 'subscriberNewsletterDelete'])->name('subscriber.newsletter.delete');
});
