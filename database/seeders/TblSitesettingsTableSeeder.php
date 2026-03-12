<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TblSitesettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tbl_sitesettings')->delete();
        
        \DB::table('tbl_sitesettings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'company_name' => 'GPS Hospitality',
                'company_mobile_no' => 1234567891,
                'company_email' => NULL,
                'company_prefix' => 'TM',
                'domain_name' => 'gps@gmail.com',
                'smtp' => 'mail.iws.in',
                'auth_email' => 'gps@gmail.com',
                'auth_email_username' => 'gps@gmail.com',
                'auth_email_password' => 'Newpass@890',
                'info_email' => NULL,
                'cc_email' => NULL,
                'bcc_email' => NULL,
                'url_rewrite' => '1',
                'default_meta_title' => NULL,
                'default_meta_keyword' => NULL,
                'default_meta_description' => NULL,
                'is_allow_gst' => 0,
                'status' => 1,
                'website_markup' => 5.0,
                'ru_hash' => '309A76131CA21D990349B717AE0AA4B3EAC123B6',
                'blocking_hour' => 1,
                'created_at' => NULL,
                'updated_at' => '2025-09-08 06:34:43',
                'deleted_at' => NULL,
                'booking_manager_name' => 'Sir/Ma\'am',
                'booking_manager_email' => 'taniya@iws.in',
                'vacation_manager_name' => 'Taniya',
                'vacation_manager_email' => 'puneet@iws.in',
                'vacation_manager_mobile_no' => '9999999999',
                'account_user_name' => NULL,
                'account_user_email' => 'taniya@iws.in',
                'site_team_name' => NULL,
                'site_team_email' => 'taniya@iws.in',
                'home_since' => '2014',
                'home_staff' => '100',
                'hosted_families' => '5000',
                'account_no' => '000000000000000',
                'PaymentMethodID' => 'VISA, MASTERCARD, AMERICAN EXPRESS',
                'LicenceNumber' => '1222233444',
                'currency_in_inr' => 0,
            ),
        ));
        
        
    }
}