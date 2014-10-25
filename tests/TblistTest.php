<?php

use Nerweb\Tblist\Tblist;

class TblistTest extends PHPUnit_Framework_TestCase
{
    public function testSampleTest()
    {



        $json = '[{
             "id": 38,
          "salutations": "Alvena",
          "first_name": "Wilderman",
          "last_name": "Dustin",
          "email": "nspencer@hotmail.com",
          "password": "$2y$10$XE2spQVpOsVixkyo5KdGM./QdeoAG9p3VglYpuIspRtazV06AL1SC",
          "address1": "408 Bartoletti Brook\nPort Marilyne, MO 80430",
          "address2": "Suite 525",
          "city": "Hammesberg",
          "state": "South Dakota",
          "postal_code": "39071",
          "country_id": 1,
          "currency_id": 1,
          "timezone_id": 1,
          "date_format_id": 1,
          "organization_name": "syndicate magnetic schemas",
          "website": "lemke.com",
          "work_phone": "323-638-6560x54168",
          "private_notes": "Fuga sit dolorum sit aspernatur unde laborum eveniet aut ex rerum officia qui ab fuga repellendus occaecati qui.",
          "balance": 90027455.70,
          "paid_to_date": 1970.00,
          "user_type": "Private",
          "status": "Cancelled",
          "last_login": "1983-01-13 13:14:40",
          "registration_date": "2007-05-19",
          "confirmation_code": "d68d9796298e43f170aa84ba2eed86f9398be8e52c70475cac7ec2e7e39bd7a4",
          "confirmed": 1,
          "remember_token": "df44aa0a93f25bedb78c6f2ca4bccd37b3271c2b",
          "created_at": "2014-10-07 15:07:24",
          "updated_at": "2014-10-07 15:07:24"
         },
         {
             "id": 42,
          "salutations": "Marquise",
          "first_name": "Leuschke",
          "last_name": "D\'angelo",
          "email": "janet99@gleason.com",
          "password": "$2y$10$N.BWsb95GuYuesA71xuiXuc/Ts7q6l0oh23EZY1S8FXf5SbwNTpG2",
          "address1": "8473 Norberto Spurs Apt. 721\nNorth Janae, FL 26747-7679",
          "address2": "Apt. 276",
          "city": "Waelchiborough",
          "state": "California",
          "postal_code": "33633",
          "country_id": 1,
          "currency_id": 1,
          "timezone_id": 1,
          "date_format_id": 1,
          "organization_name": "optimize sticky schemas",
          "website": "donnelly.net",
          "work_phone": "447.493.5616",
          "private_notes": "Quod et quasi id mollitia consequatur doloremque voluptatum ut fuga sed nisi laboriosam facilis molestiae et et.",
          "balance": 16152.59,
          "paid_to_date": 1994.00,
          "user_type": "Private",
          "status": "Cancelled",
          "last_login": "1979-06-04 12:15:32",
          "registration_date": "1987-03-09",
          "confirmation_code": "379f528647c312c1f030347dc356ade3995d5caa554fe97c9258fddf991d7262",
          "confirmed": 1,
          "remember_token": "e30f98f2fbc6d5d1b64fff37b98a3e68cc2c94db",
          "created_at": "2014-10-07 15:07:24",
          "updated_at": "2014-10-07 15:07:24"
         },
         {
             "id": 43,
          "salutations": "Vella",
          "first_name": "Wehner",
          "last_name": "Immanuel",
          "email": "howard11@gmail.com",
          "password": "$2y$10$5hg0nzuiRkapgGP3waGg5eyHf0Ejmjho1FKL5220Uq4Wg0mxvsSr.",
          "address1": "4233 Johnson Tunnel Suite 271\nHomenickmouth, SD 70597-8452",
          "address2": "Suite 666",
          "city": "East Laceystad",
          "state": "Wyoming",
          "postal_code": "76762-7213",
          "country_id": 1,
          "currency_id": 1,
          "timezone_id": 1,
          "date_format_id": 1,
          "organization_name": "engineer strategic action-items",
          "website": "kertzmannheaney.com",
          "work_phone": "+81(4)8620334081",
          "private_notes": "Adipisci autem veritatis sed dolorum aut tempore blanditiis repellat consectetur sit et nostrum animi officiis at neque dolorem sit in quia et occaecati delectus autem odio.",
          "balance": 2099.68,
          "paid_to_date": 1990.00,
          "user_type": "Private",
          "status": "Pending Verification",
          "last_login": "1988-10-26 10:51:36",
          "registration_date": "2013-02-26",
          "confirmation_code": "a426d42ea5c762b907a14321aa0bb76863c819c3aefd58e3b88ba78b1550cfae",
          "confirmed": 1,
          "remember_token": "42c84ce791d4fefb30acdeab4198dac377a05f13",
          "created_at": "2014-10-07 15:07:25",
          "updated_at": "2014-10-07 15:07:25"
         },
         {
             "id": 44,
          "salutations": "Rudy",
          "first_name": "Waelchi",
          "last_name": "Dillan",
          "email": "marguerite12@kihnwaters.com",
          "password": "$2y$10$VXTg4sjjjMu.OEYLClajqeiHgbmZClzB/7i3Ge9bvNew6nSI7V.wS",
          "address1": "4764 Towne Lodge\nSouth Kadin, NV 70855-2123",
          "address2": "Apt. 902",
          "city": "West Hilmaside",
          "state": "Kansas",
          "postal_code": "64853-4452",
          "country_id": 1,
          "currency_id": 1,
          "timezone_id": 1,
          "date_format_id": 1,
          "organization_name": "transition synergistic metrics",
          "website": "gibson.com",
          "work_phone": "1-161-848-1944",
          "private_notes": "Eos quo accusantium est ipsa aut rerum autem enim quo nihil repellendus consequatur.",
          "balance": 209.60,
          "paid_to_date": 2004.00,
          "user_type": "Private",
          "status": "Closed",
          "last_login": "2004-01-04 02:56:36",
          "registration_date": "1983-06-03",
          "confirmation_code": "c779ef6fb5be0e296857ac97c4eaac48ff730b187280fb832ced9d7fdc654fc0",
          "confirmed": 1,
          "remember_token": "4d95e9210519780ff2e3c27127fd3dce4a5918d8",
          "created_at": "2014-10-07 15:07:25",
          "updated_at": "2014-10-07 15:07:25"
         }]';

        $array = json_decode($json);


    }


    public function testHttpWithMax25Characters()
    {


        $string = 'QS3PE5ZGdxC9IoVKTAPT2DBYpPkMKqfz.com asdf asdf www.leadingsdfsdfedgeweb.com.au/contact.php adfasdfasd http://www.leadingedgeweb.com.au/contact.php';
        if ( ! isValidMailMessageFieldString($string))
        {
            echo "yes invalid";
        }
        else
        {
            echo "yes valid";
        }
    }


}