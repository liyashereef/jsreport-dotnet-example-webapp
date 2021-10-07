<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use ConsoleTVs\Charts\Registrar as Charts;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Charts $charts)
    {
        //
        Schema::defaultStringLength(191);
        Validator::extend('sum', function ($attribute, $value, $parameters) {
            if (count($parameters) !== 1) {
                throw new InvalidArgumentException('Validation rule sum requires exactly 1 parameter.');
            }

            return array_sum($value) >= $parameters[0];
        });
         Validator::replacer('sum', function ($message, $attribute, $rule, $parameters) {
            return 'The sum of weights should be 100';
         });
        Validator::extend('greater_than_field', function ($attribute, $value, $parameters, $validator) {
            $min_field = $parameters[0];
            $data = $validator->getData();
            $min_value = $data[$min_field];
            return $value >= $min_value;
        });

        Validator::replacer('greater_than_field', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });
        Validator::extend('unique_multiple', function ($attribute, $value, $parameters, $validator) {
             $table = $parameters[0];
               $exists = \DB::table($table)->where($parameters[1], $validator->getData()[$parameters[1]])->where($parameters[2], $validator->getData()[$parameters[2]])->where($parameters[3], $validator->getData()[$parameters[3]])->where($parameters[4], $validator->getData()[$parameters[4]])->exists();
                return !$exists;
        });
        Validator::replacer('unique_multiple', function ($message, $attribute, $rule, $parameters) {
               return 'This service has been already taken up';
              // $replaced_text=str_replace(':field1', $parameters[1], $message);
              // $replaced_text=str_replace(':field2', $parameters[2], $replaced_text);
              //return str_replace(':field3', $parameters[3], $message);
        });

        Validator::extend('year_greater_than', function ($attribute, $value, $parameters, $validator) {
            $year_from_purchase_date=date('Y', strtotime($value));
            $year = $parameters[0];
            $data = $validator->getData();
            $year = $data[$year];
            return $year_from_purchase_date >= $year;
        });

        Validator::replacer('year_greater_than', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':field', $parameters[0], $message);
        });

        Validator::extend('recaptcha', 'App\\Rules\\ReCaptcha@validate');

        $charts->register([
            \App\Charts\ComplianceChart::class
        ]);
    }
}
