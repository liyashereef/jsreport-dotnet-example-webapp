<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Admin\Models\Customer;
use Illuminate\Support\Str;

class ScrubDataMethodCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $customers=Customer::get();
        foreach ($customers as $customer) {
            $id=$customer->id;
            $company=$faker->company();
            $domain=strtolower(str_replace( array( '\'', '"',
            ',' , ';', '<', '>' ), ' ',str_replace(" ","",$company)).".com");
            $contactPerson=$faker->name;
            $cellphone=($faker->numerify('##########'));
            $projectno=($faker->numerify('#######'));
            $idNumber=(string) Str::uuid();
            $contactPersonEmail=str_replace(" ","",strtolower($contactPerson) ."@".$domain);
            $requestPerson=$faker->name;
            $requestPersonEmail=str_replace(" ","",strtolower($requestPerson))."@".$domain;
            $customerData=Customer::find($id);
            $customerData->client_name=$company;
            $customerData->project_number=$projectno;
            $customerData->contact_person_name=$contactPerson;
            $customerData->contact_person_email_id=$contactPersonEmail;
            $customerData->contact_person_phone=$faker->phoneNumber;
            $customerData->contact_person_cell_phone=$cellphone;
            $customerData->requester_name=$requestPerson;
            $customerData->requester_empno=$idNumber;
            // $customerData->postal_code=$faker->postcode();
            $customerData->billing_address=$faker->address;
            $customerData->qr_recipient_email=$requestPersonEmail;
            $customerData->save();
        }
    }
}
