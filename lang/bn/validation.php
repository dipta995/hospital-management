<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute গ্রহণ করতে হবে।',
    'accepted_if' => ':other :value হলে :attribute গ্রহণ করতে হবে।',
    'active_url' => ':attribute একটি বৈধ URL হতে হবে।',
    'after' => ':attribute অবশ্যই :date-এর পরে একটি তারিখ হতে হবে।',
    'after_or_equal' => ':attribute অবশ্যই :date-এর পরে বা সমান তারিখ হতে হবে।',
    'alpha' => ':attribute শুধুমাত্র অক্ষর থাকতে পারবে।',
    'alpha_dash' => ':attribute শুধুমাত্র অক্ষর, সংখ্যা, ড্যাশ এবং আন্ডারস্কোর থাকতে পারবে।',
    'alpha_num' => ':attribute শুধুমাত্র অক্ষর এবং সংখ্যা থাকতে পারবে।',
    'array' => ':attribute একটি অ্যারে হতে হবে।',
    'before' => ':attribute অবশ্যই :date-এর পূর্বে একটি তারিখ হতে হবে।',
    'before_or_equal' => ':attribute অবশ্যই :date-এর পূর্বে বা সমান তারিখ হতে হবে।',
    'between' => [
        'array' => ':attribute-এর মধ্যে :min থেকে :max উপাদান থাকতে হবে।',
        'file' => ':attribute অবশ্যই :min থেকে :max কিলোবাইটের মধ্যে হতে হবে।',
        'numeric' => ':attribute অবশ্যই :min থেকে :max এর মধ্যে হতে হবে।',
        'string' => ':attribute অবশ্যই :min থেকে :max অক্ষরের মধ্যে হতে হবে।',
    ],
    'boolean' => ':attribute ফিল্ডটি সত্য বা মিথ্যা হতে হবে।',
    'confirmed' => ':attribute নিশ্চিতকরণ মিলছে না।',
    'current_password' => 'পাসওয়ার্ডটি ভুল।',
    'date' => ':attribute একটি বৈধ তারিখ হতে হবে।',
    'date_equals' => ':attribute অবশ্যই :date-এর সমান একটি তারিখ হতে হবে।',
    'date_format' => ':attribute ফরম্যাট :format-এর সাথে মিলছে না।',
    'decimal' => ':attribute অবশ্যই :decimal দশমিক স্থান সহ হতে হবে।',
    'different' => ':attribute এবং :other আলাদা হতে হবে।',
    'digits' => ':attribute অবশ্যই :digits সংখ্যার হতে হবে।',
    'digits_between' => ':attribute অবশ্যই :min থেকে :max সংখ্যার মধ্যে হতে হবে।',
    'email' => ':attribute একটি বৈধ ইমেইল ঠিকানা হতে হবে।',
    'exists' => 'নির্বাচিত :attribute অবৈধ।',
    'file' => ':attribute অবশ্যই একটি ফাইল হতে হবে।',
    'filled' => ':attribute ফিল্ডটি অবশ্যই পূরণ করতে হবে।',
    'gt' => [
        'array' => ':attribute-এ :value-এর বেশি উপাদান থাকতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের বেশি হতে হবে।',
        'numeric' => ':attribute অবশ্যই :value-এর বেশি হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের বেশি হতে হবে।',
    ],
    'gte' => [
        'array' => ':attribute-এ কমপক্ষে :value উপাদান থাকতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইট বা তার বেশি হতে হবে।',
        'numeric' => ':attribute অবশ্যই :value বা তার বেশি হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষর বা তার বেশি হতে হবে।',
    ],
    'image' => ':attribute অবশ্যই একটি ইমেজ হতে হবে।',
    'in' => 'নির্বাচিত :attribute অবৈধ।',
    'integer' => ':attribute অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
    'max' => [
        'array' => ':attribute-এ :max-এর বেশি উপাদান থাকতে পারবে না।',
        'file' => ':attribute অবশ্যই :max কিলোবাইটের বেশি হতে পারবে না।',
        'numeric' => ':attribute অবশ্যই :max-এর বেশি হতে পারবে না।',
        'string' => ':attribute অবশ্যই :max অক্ষরের বেশি হতে পারবে না।',
    ],
    'min' => [
        'array' => ':attribute-এ কমপক্ষে :min উপাদান থাকতে হবে।',
        'file' => ':attribute অবশ্যই অন্তত :min কিলোবাইট হতে হবে।',
        'numeric' => ':attribute অবশ্যই অন্তত :min হতে হবে।',
        'string' => ':attribute অবশ্যই অন্তত :min অক্ষরের হতে হবে।',
    ],
    'not_in' => 'নির্বাচিত :attribute অবৈধ।',
    'numeric' => ':attribute অবশ্যই একটি সংখ্যা হতে হবে।',
    'regex' => ':attribute-এর ফরম্যাট অবৈধ।',
    'required' => ':attribute ফিল্ডটি প্রয়োজন।',
    'same' => ':attribute এবং :other অবশ্যই মিলতে হবে।',
    'size' => [
        'array' => ':attribute অবশ্যই :size উপাদান ধারণ করতে হবে।',
        'file' => ':attribute অবশ্যই :size কিলোবাইট হতে হবে।',
        'numeric' => ':attribute অবশ্যই :size হতে হবে।',
        'string' => ':attribute অবশ্যই :size অক্ষরের হতে হবে।',
    ],
    'string' => ':attribute অবশ্যই একটি স্ট্রিং হতে হবে।',
    'unique' => ':attribute ইতিমধ্যে নেওয়া হয়েছে।',
    'url' => ':attribute একটি বৈধ URL হতে হবে।',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'The :attribute field must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
