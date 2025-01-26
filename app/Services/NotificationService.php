<?php

namespace App\Services;

class NotificationService
{

    public function sendOrderMatchedSms(string $sellerPhoneNumber,string $buyerPhoneNumber):void
    {
        //TODO We must config a Notification Service
        // (e.x SmsSenderService) with user foreign key and user phoneNumber.
        // Add laravel Translator to create suitable message for seller and buyer
    }
}
