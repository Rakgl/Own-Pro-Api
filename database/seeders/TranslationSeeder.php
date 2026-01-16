<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->getAllTranslations()
            ->each(fn ($item) => $this->saveTranslation($item));
    }

    /**
     * Helper to persist translation to the database.
     */
    private function saveTranslation(array $data): void
    {
        Translation::updateOrCreate(
            [
                'key'      => $data['key'],
                'platform' => $data['platform'] ?? 'ADMIN',
            ],
            [
                'value'  => json_encode([
                    'en' => $data['en'],
                    'kh' => $data['kh'],
                ]),
                'status' => $data['status'] ?? 'ACTIVE',
            ]
        );
    }

    /**
     * Merges all translation groups into one collection.
     */
    private function getAllTranslations(): Collection
    {
        return collect([
            ...$this->getGlobalTranslations(),
            ...$this->getNavMenuTranslations(),
            ...$this->getDataTableTranslations(),
        ]);
    }

    /**
     * Global application strings.
     */
    private function getGlobalTranslations(): array
    {
        return [
            ['key' => 'account_settings', 'en' => 'Account Settings', 'kh' => 'ការកំណត់គណនី', 'platform' => 'MOBILE'],
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
     * Navigation and Sidebar menu strings.
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

    /**
     * Data Table components and dynamic feedback strings.
     */
    private function getDataTableTranslations(): array
    {
        return [
            ['key' => 'common.index', 'en' => '#', 'kh' => 'ល.រ'],
            ['key' => 'common.name', 'en' => 'Name', 'kh' => 'ឈ្មោះ'],
            ['key' => 'common.description', 'en' => 'Description', 'kh' => 'ការពិពណ៌នា'],
            ['key' => 'common.status', 'en' => 'Status', 'kh' => 'ស្ថានភាព'],
            ['key' => 'common.actions', 'en' => 'Actions', 'kh' => 'សកម្មភាព'],
            ['key' => 'common.active', 'en' => 'Active', 'kh' => 'សកម្ម'],
            ['key' => 'common.inactive', 'en' => 'Inactive', 'kh' => 'មិនសកម្ម'],
            ['key' => 'common.unknown', 'en' => 'UNKNOWN', 'kh' => 'មិនស្គាល់'],
            ['key' => 'common.asc', 'en' => 'Asc', 'kh' => 'លំដាប់ឡើង'],
            ['key' => 'common.desc', 'en' => 'Desc', 'kh' => 'លំដាប់ចុះ'],
            ['key' => 'common.hide', 'en' => 'Hide', 'kh' => 'លាក់'],
            ['key' => 'common.reset', 'en' => 'Reset', 'kh' => 'កំណត់ឡើងវិញ'],
            ['key' => 'common.view_options', 'en' => 'View', 'kh' => 'បង្ហាញ'],
            ['key' => 'common.toggle_columns', 'en' => 'Toggle columns', 'kh' => 'ជ្រើសរើសជួរ'],
            ['key' => 'common.clear_filters', 'en' => 'Clear filters', 'kh' => 'សម្អាតការជ្រើសរើស'],
            ['key' => 'common.rows_per_page', 'en' => 'Rows per page', 'kh' => 'ចំនួនជួរក្នុងមួយទំព័រ'],
            ['key' => 'common.enter_name', 'en' => 'Enter name', 'kh' => 'បញ្ចូលឈ្មោះ'],
            ['key' => 'common.update_success', 'en' => 'Updated successfully!', 'kh' => 'បានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ!'],
            ['key' => 'common.create_success', 'en' => 'Created successfully!', 'kh' => 'បានបង្កើតដោយជោគជ័យ!'],
            ['key' => 'common.delete_success', 'en' => 'Deleted successfully!', 'kh' => 'បានលុបដោយជោគជ័យ!'],
            ['key' => 'common.permissions_updated', 'en' => 'Permissions updated!', 'kh' => 'បានធ្វើបច្ចុប្បន្នភាពសិទ្ធិរួចរាល់!'],
            ['key' => 'common.are_you_sure', 'en' => 'Are you absolutely sure?', 'kh' => 'តើអ្នកប្រាកដជាចង់ធ្វើសកម្មភាពនេះមែនទេ?'],
            ['key' => 'nav.add_new_role', 'en' => 'Add New Role', 'kh' => 'បន្ថែមតួនាទីថ្មី'],
            ['key' => 'common.selected_count', 'en' => '{count} selected', 'kh' => 'បានជ្រើសរើស {count}'],
            ['key' => 'common.page_info', 'en' => 'Page {current} of {total}', 'kh' => 'ទំព័រទី {current} នៃ {total}'],
            ['key' => 'common.selected_rows', 'en' => '{count} of {total} row(s) selected.', 'kh' => 'បានជ្រើសរើស {count} ក្នុងចំណោម {total} ជួរ។'],
            ['key' => 'common.delete_warning', 'en' => 'This action cannot be undone. This will permanently delete "{name}" and remove the data.', 'kh' => 'សកម្មភាពនេះមិនអាចត្រឡប់ក្រោយបានទេ។ វានឹងលុប "{name}" ជាអចិន្ត្រៃយ៍ពីប្រព័ន្ធ។'],
            ['key' => 'common.edit_description', 'en' => 'Make changes to the details here. Click save when you\'re done.', 'kh' => 'កែប្រែព័ត៌មានលម្អិតនៅទីនេះ។ ចុចរក្សាទុកនៅពេលអ្នករួចរាល់។'],
            ['key' => 'common.create_role_description', 'en' => 'Define the properties for the new role. Required fields are marked with (*).', 'kh' => 'កំណត់លក្ខណៈសម្បត្តិសម្រាប់តួនាទីថ្មី។ ផ្នែកដែលចាំបាច់ត្រូវបានចំណាំដោយ (*).'],
        ];
    }
}