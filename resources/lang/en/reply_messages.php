<?php

$url = url('');

return [
    'frontend_api' => [
        'item_not_found' => ':item not found',
    ],

    'bot' => [
        'greeting' => "สวัสดี :PLACEHOLDER 😃\n\n Welcome to the Club!! ✌️",
        'user_not_registered' => "ขออภัย :PLACEHOLDER 🙏\nยังไม่สามารถให้บริการท่านได้ \n\nโปรดทำการลงทะเบียนก่อนที่ {$url} (กรุณาเปิด link นี้ใน web browser)\n\n😅\n\nเมื่อทำการลงทะเบียนแล้วอย่าลืม :STOP และ :RESTART bot ด้วยน๊า 🤗",
    ],
];
