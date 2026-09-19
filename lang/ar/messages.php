<?php
return [
  // Success messages.
  // 'register_success' => 'registered in successfully!',
  'login_success'   => 'تم تسجيل الدخول بنجاح!',
  'logout_success'  => 'تم تسجيل الخروج بنجاح!',
  'password_reset'  => 'تم إعادة تعيين كلمة المرور بنجاح!',
  'otp_sent'        => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.',
  'otp_verified'    => 'تم التحقق من الرمز بنجاح!',
  'token_generated' => 'تم إنشاء رمز الدخول بنجاح!',

  // Resources.
  'resources' => [
    'resource'     => ['singular' => 'المورد',               'plural'  => 'الموارد'],
    'admin'        => ['singular' => 'المسؤول',              'plural'  => 'المسؤولين'],
    'user'         => ['singular' => 'المستخدم',             'plural'  => 'المستخدمين'],
    'banner'       => ['singular' => 'اللافتة',               'plural'  => 'اللافتات'],
    'category'     => ['singular' => 'التصنيف',              'plural'  => 'التصنيفات'],
    'phonenumber'  => ['singular' => 'رقم الهاتف',           'plural'  => 'أرقام الهواتف'],
    'product'      => ['singular' => 'المنتج',               'plural'  => 'المنتجات'],
    'productimage' => ['singular' => 'الصورة',               'plural'  => 'الصور'],
    'image'        => ['singular' => 'الصورة',               'plural'  => 'الصور'],
    'all_images'   => ['singular' => 'كل الصور',             'plural'  => 'كل الصور'],
    'profile'      => ['singular' => 'بيانات الملف الشخصي', 'plural'  => 'بيانات الملفات الشخصية'],
    'sociallink'   => ['singular' => 'الرابط الاجتماعي',     'plural'  => 'الروابط الاجتماعية'],
  ],

  'actions' => [
    'retrieved_success'     => 'تم جلب :resource بنجاح!',
    'created_success'       => 'تم إنشاء :resource بنجاح!',
    'updated_success'       => 'تم تحديث :resource بنجاح!',
    'deleted_success'       => 'تم حذف :resource بنجاح!',
    'restored_success'      => 'تم استرجاع :resource بنجاح!',
    'force_deleted_success' => 'تم حذف :resource نهائيًا!',
    'error'                 => 'حدث خطأ أثناء تنفيذ العملية، برجاء المحاولة مرة أخرى لاحقاً.',
  ],

  // Error messages.
  'image_not_found_in_product' => 'الصورة غير موجودة في هذا المنتج!',
  'product_has_no_images'      => 'المنتج لا يحتوي على صور للحذف!',
  'cannot_delete_position'     => 'لا يمكنك حذف صورة الموضع ":attribute"، جرب استبدالها!',
  '404_not_found'              => ':model غير موجود!',
  'method_not_allowed'         => 'طريقة الطلب غير مسموح بها.',
  'too_many_requests'          => 'تم إرسال عدد كبير جدًا من الطلبات. يرجى المحاولة لاحقًا.',
  'database_error'             => 'حدث خطأ أثناء التعامل مع قاعدة البيانات.',
  'http_error'                 => 'حدث خطأ أثناء تنفيذ الطلب.',
  'internal_server_error'      => 'حدث خطأ داخلي في الخادم. يرجى المحاولة لاحقًا.',
  'file_error'                 => 'حدث خطأ أثناء التعامل مع الملف.',
  'external_api_error'         => 'حدث خطأ أثناء الاتصال بخدمة خارجية. يرجى المحاولة لاحقًا.',
  'invalid_json'               => 'تنسيق البيانات المرسلة غير صحيح.',
  'max_reached'                => 'لقد وصلتِ إلى الحد الأقصى لعدد :object المسموح بها (العدد: :max).',
  'otp_expired'                => 'انتهت صلاحية رمز التحقق هذا. اعد الارسال وحاول مرة اخري',
  'already_otp_resent'         => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني بالفعل!',
  'wait_before_resend'         => 'يرجى الانتظار :remain_seconds من الثوانٍ قبل إعادة إرسال الكود.',
  'must_verify_otp_first'      => 'يجب عليك تأكيد رمز التحقق قبل إعادة تعيين كلمة المرور.',

  // Mail messages.
  'mail' => [
    'title'             => 'تأكيد البريد الإلكتروني',
    'greeting'          => 'مرحباً',
    'body'              => 'استخدم رمز التحقق لمرة واحدة لتأكيد بريدك الإلكتروني:',
    'verify_button'     => 'التأكيد من هنا!',
    'expire'            => 'سينتهي صلاحية هذا الرمز خلال <strong>:minutes دقيقة</strong>.',
    'remaining_times'   => 'يمكنك إعادة إرسال الكود :remain مرة أخرى (من أصل :max محاولات مسموحة).',
    'ignore'            => 'إذا لم تطلب هذا، يمكنك تجاهل هذه الرسالة بأمان.',
    'footer'            => 'جميع الحقوق محفوظة.',
    'verification_code' => 'رمز التحقق',
  ]
];
