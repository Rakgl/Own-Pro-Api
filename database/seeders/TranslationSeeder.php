<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $translations = $this->getAllTranslations();

        foreach ($translations as $translation) {
            Translation::updateOrCreate(
                [
                    'key' => $translation['key'],
                    'platform' => $translation['platform'] ?? 'ADMIN',
                ],
                [
                    'value' => json_encode([
                        'en' => $translation['en'],
                        'kh' => $translation['kh'],
                    ]),
                    'status' => $translation['status'] ?? 'ACTIVE',
                ]
            );
        }
    }

    /**
     * Aggregates all translation arrays into a single array.
     *
     * @return array
     */
    private function getAllTranslations(): array
    {
        return array_merge(
            $this->getGlobalTranslations(),
            $this->getNavMenuTranslations(),
        );
    }

    private function getGlobalTranslations(): array
    {
        return [
            ['key' => 'account_settings', 'en' => 'Account Settings', 'kh' => 'ការកំណត់គណនី', 'platform' => 'MOBILE', 'status' => 'ACTIVE'],
            ['key' => 'welcome', 'en' => 'Welcome to our application!', 'kh' => 'សូមស្វាគមន៍មកកាន់កម្មវិធីរបស់យើង!'],
            ['key' => 'hello_name', 'en' => 'Hello, {name}!', 'kh' => 'ជំរាបសួរ, {name}!'],
            ['key' => 'select_language', 'en' => 'Select Language', 'kh' => 'ជ្រើសរើសភាសា'],
            ['key' => 'dashboard', 'en' => 'Dashboard', 'kh' => 'ផ្ទាំងគ្រប់គ្រង'],
            ['key' => 'users', 'en' => 'Users', 'kh' => 'អ្នកប្រើប្រាស់'],
            ['key' => 'loading', 'en' => 'Loading...', 'kh' => 'កំពុងផ្ទុក...'],
            ['key' => 'error', 'en' => 'An error occurred.', 'kh' => 'មានកំហុសកើតឡើង។'],
            ['key' => 'success', 'en' => 'Success!', 'kh' => 'ជោគជ័យ!'],
            ['key' => 'actions.edit', 'en' => 'Edit', 'kh' => 'កែសម្រួល'],
            ['key' => 'actions.delete', 'en' => 'Delete', 'kh' => 'លុប'],
            ['key' => 'actions.save', 'en' => 'Save Changes', 'kh' => 'រក្សាទុកការផ្លាស់ប្តូរ'],
            ['key' => 'actions.cancel', 'en' => 'Cancel', 'kh' => 'បោះបង់'],
            ['key' => 'themeModal.title', 'en' => 'Customize', 'kh' => 'ប្ដូរតាមបំណង'],
            ['key' => 'themeModal.description', 'en' => 'Customize & Preview in Real Time', 'kh' => 'ប្ដូរតាមបំណង និងមើលជាមុនក្នុងពេលជាក់ស្ដែង'],
            ['key' => 'sidebar.menu.profile', 'en' => 'Profile', 'kh' => 'ប្រវត្តិរូប'],
            ['key' => 'sidebar.menu.account', 'en' => 'Account', 'kh' => 'គណនី'],
            ['key' => 'sidebar.menu.settings', 'en' => 'Settings', 'kh' => 'ការកំណត់'],
            ['key' => 'sidebar.menu.appearance', 'en' => 'Appearance', 'kh' => 'រូបរាង'],
            ['key' => 'sidebar.menu.logout', 'en' => 'Log out', 'kh' => 'ចាកចេញ'],
            ['key' => 'common.search', 'en' => 'Search', 'kh' => 'ស្វែងរក'],
            ['key' => 'common.search_placeholder', 'en' => 'Type a command or search...', 'kh' => 'វាយបញ្ចូលពាក្យបញ្ជា ឬស្វែងរក...'],
            ['key' => 'common.no_results', 'en' => 'No results found.', 'kh' => 'រកមិនឃើញលទ្ធផល'],
            ['key' => 'common.welcome_back', 'en' => 'Welcome back!', 'kh' => 'សូមស្វាគមន៍មកវិញ!'],
        ];
    }

    /**
     * Returns navigation menu translations.
     * @return array
     */
    private function getNavMenuTranslations(): array
    {
        return [
            ['key' => 'nav.dashboard', 'en' => 'Dashboard', 'kh' => 'ផ្ទាំងគ្រប់គ្រង'],
            ['key' => 'nav.core_administration', 'en' => 'Core Administration', 'kh' => 'ការកំណត់គណនី'],
            ['key' => 'nav.authentication', 'en' => 'Authentication', 'kh' => 'ការផ្ទៀងផ្ទាត់ភាពត្រឹមត្រូវ'],
            ['key' => 'nav.system_users', 'en' => 'System Users', 'kh' => 'អ្នកប្រើប្រាស់ប្រព័ន្ធ'],
            ['key' => 'nav.role_permission', 'en' => 'Role & Permission', 'kh' => 'តួនាទី និងសិទ្ធិ'],
            ['key' => 'nav.translation', 'en' => 'Translations', 'kh' => 'ការគ្រប់គ្រងការបកប្រែ'],
            ['key' => 'nav.property_management', 'en' => 'Property Management', 'kh' => 'ការគ្រប់គ្រងអចលនទ្រព្យ'],
            ['key' => 'nav.room_list', 'en' => 'Room List', 'kh' => 'បញ្ជីបន្ទប់'],
            ['key' => 'nav.utility_management', 'en' => 'Utility Management', 'kh' => 'ការគ្រប់គ្រងទឹកភ្លើង'],
            ['key' => 'nav.billing_invoices', 'en' => 'Billing & Invoices', 'kh' => 'ការវិក្កយបត្រ'],
            ['key' => 'nav.app_management', 'en' => 'App Management', 'kh' => 'ការគ្រប់គ្រងកម្មវិធី'],
            ['key' => 'nav.app_banner', 'en' => 'App Banner', 'kh' => 'បាដាកម្មវិធី'],
            ['key' => 'nav.push_notifications_log', 'en' => 'Push Notifications Log', 'kh' => 'កំណត់ហេតុការជូនដំណឹង'],
            ['key' => 'nav.product_sales', 'en' => 'Product Sales', 'kh' => 'ការលក់ផលិតផល'],
            ['key' => 'nav.product_inventory', 'en' => 'Product Inventory', 'kh' => 'សារពើភ័ណ្ឌផលិតផល'],
            ['key' => 'nav.all_orders', 'en' => 'All Orders', 'kh' => 'ការបញ្ជាទិញទាំងអស់'],
            ['key' => 'nav.sales_reports', 'en' => 'Sales Reports', 'kh' => 'របាយការណ៍លក់'],
            ['key' => 'nav.audit_logs', 'en' => 'System Audit Logs', 'kh' => 'កំណត់ហេតុត្រួតពិនិត្យប្រព័ន្ធ'],
            ['key' => 'nav.help_support', 'en' => 'Help & Support', 'kh' => 'ជំនួយ និងការគាំទ្រ'],
            ['key' => 'nav.feedback', 'en' => 'Feedback', 'kh' => 'មតិកែលម្អ'],
            ['key' => 'nav.items.account_recovery', 'en' => 'Account Recovery', 'kh' => 'ការស្តារគណនី'],
            ['key' => 'nav.items.ai_assitant', 'en' => 'AI Assistant', 'kh' => 'ជំនួយការ AI'],
            ['key' => 'nav.items.ocr', 'en' => 'OCR Scanner', 'kh' => 'ម៉ាស៊ីនស្កេន OCR'],
        ];
    }
}