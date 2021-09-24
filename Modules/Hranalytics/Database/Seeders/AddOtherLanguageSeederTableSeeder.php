<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;
use Modules\Admin\Models\Languages;

class AddOtherLanguageSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->truncate();
        $languageArray = [
            'Algerian Arabic',
            'Amharic', 'Assamese', 'Bavarian', 'Bengali',
            'Bhojpuri', 'Burmese', 'Cebuano',
            'Chhattisgarhi', 'Chittagonian',
            'Czech', 'Deccan',
            'Dutch',
            'Egyptian Arabic',
            'Gan Chinese',
            'German',
            'Greek',
            'Gujarati',
            'Hakka Chinese',
            'Hausa',
            'Hejazi Arabic',
            'Hindi',
            'Hungarian',
            'Igbo', 'Indonesian', 'Iranian Persian',
            'Italian', 'Japanese', 'Javanese',
            'Jin Chinese', 'Kannada', 'Kazakh',
            'Khmer', 'Kinyarwanda', 'Korean',
            'Magahi', 'Maithili', 'Malayalam',
            'Malaysian Malay', 'Mandarin Chinese',
            'Marathi', 'Mesopotamian Arabic',
            'Min Bei Chinese', 'Min Dong Chinese',
            'Min Nan Chinese', 'Moroccan Arabic',
            'Nepali', 'Nigerian Fulfulde',
            'North Levantine Arabic', 'Northern Kurdish',
            'Northern Pashto', 'Northern Uzbek', 'Odia',
            'Polish', 'Portuguese', 'Punjabi', 'Romanian',
            'Rundi', 'Russian', 'Saudi Arabic',
            'Sanaani Spoken Arabic', 'Saraiki',
            'Sindhi', 'Sinhalese', 'Somali',
            'South Azerbaijani', 'South Levantine Arabic',
            'Southern Pashto', 'Spanish', 'Sudanese Arabic',
            'Sunda', 'Sylheti', 'Tagalog',
            'Taizzi-Adeni Arabic', 'Tamil', 'Telugu',
            'Thai', 'Tunisian Arabic', 'Turkish',
            'Ukrainian', 'Urdu', 'Uyghur', 'Vietnamese',
            'Western Punjabi', 'Wu Chinese', 'Xiang Chinese',
            'Yoruba', 'Yue Chinese', 'Zulu'
        ];
        foreach ($languageArray as $key => $value) {
            $language = $value;
            $array = ["language" => $language];
            Languages::insert($array);
        }
    }
}
