<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserCertificateExpiryEmailTemplateTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        \DB::table('email_templates')->whereIn('type_id', array(14, 15, 16,17))->delete();
        
        \DB::table('email_templates')->insert(array(
            0 =>
            array(
                'type_id' => 14,
                'email_subject' => 'Document Expiry Email Remainder 1',
                'email_body' => '<p>Hello {receiverFullName},</p><p>Certificate Name:{expiredUserCertificateName}, Expiry Date:{userCertificateExpiryDate} ({userCertificateExpiryDueInDays})</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'type_id' => 15,
                'email_subject' => 'Document Expiry Email Remainder 2',
                'email_body' => '<p>Hello {receiverFullName},</p><p>Certificate Name:{expiredUserCertificateName}, Expiry Date:{userCertificateExpiryDate} ({userCertificateExpiryDueInDays})</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'type_id' => 16,
                'email_subject' => 'Document Expiry Email Remainder 3',
                'email_body' => '<p>Hello {receiverFullName},</p><p>Certificate Name:{expiredUserCertificateName}, Expiry Date:{userCertificateExpiryDate} ({userCertificateExpiryDueInDays})</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'type_id' => 17,
                'email_subject' => 'Document Expiry Report Notification',
                'email_body' => '<p>Hello {receiverFullName},</p><p>Certificate Name:{expiredUserCertificateName}, Expiry Date:{userCertificateExpiryDate} ({userCertificateExpiryDueInDays})</p>',
                'created_at' => '2019-05-21 06:04:47',
                'updated_at' => '2019-05-21 06:04:47',
                'deleted_at' => NULL,
            ),
        ));
    }

}
