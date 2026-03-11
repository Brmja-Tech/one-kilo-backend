<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Faq;
use App\Models\Privacy;
use App\Models\Setting;
use App\Models\Terms;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultLogo = 'uploads/images/logo.png';
        $defaultImage = 'uploads/images/image.png';

        $settings = Setting::query()->firstOrNew([]);
        $settings->fill([
            'site_name' => [
                'en' => 'One Kilo',
                'ar' => 'ون كيلو',
            ],
            'site_title' => [
                'en' => 'One Kilo | Grocery delivery and daily essentials',
                'ar' => 'ون كيلو | مقاضي وسوبر ماركت بتوصيل سريع',
            ],
            'site_desc' => [
                'en' => 'Order fresh groceries, pantry staples, drinks, and household essentials with fast, reliable delivery.',
                'ar' => 'اطلب الخضار والفاكهة والمنتجات اليومية ومستلزمات البيت بتوصيل سريع وموثوق.',
            ],
            'site_address' => [
                'en' => 'Cairo, Egypt',
                'ar' => 'القاهرة، مصر',
            ],
            'meta_key' => [
                'en' => 'grocery delivery, supermarket, fresh food, vegetables, fruits, household essentials, offers',
                'ar' => 'توصيل مقاضي, سوبر ماركت, خضار, فاكهة, مواد غذائية, مستلزمات منزلية, عروض',
            ],
            'meta_desc' => [
                'en' => 'One Kilo helps shoppers order fresh groceries and everyday essentials with clear pricing and dependable support.',
                'ar' => 'ون كيلو يساعد العملاء على طلب المقاضي والاحتياجات اليومية بأسعار واضحة ودعم سريع.',
            ],
            'site_phone' => '+201000000000',
            'site_email' => 'hello@onekilo.test',
            'email_support' => 'support@onekilo.test',
            'facebook' => 'https://facebook.com/onekiloapp',
            'x_url' => 'https://x.com/onekiloapp',
            'youtube' => 'https://youtube.com/@onekiloapp',
            'instagram' => 'https://instagram.com/onekiloapp',
            'tiktok' => 'https://tiktok.com/@onekiloapp',
            'linkedin' => 'https://linkedin.com/company/onekiloapp',
            'whatsapp' => '+201000000000',
            'logo' => $defaultLogo,
            'favicon' => $defaultLogo,
            'site_copyright' => '(c) ' . now()->year . ' One Kilo. All rights reserved.',
            'promotion_url' => 'https://onekilo.test/offers/fresh-weekly-deals',
        ]);
        $settings->save();

        $this->syncPageContent(
            About::class,
            [
                'title' => [
                    'en' => 'About One Kilo',
                    'ar' => 'عن ون كيلو',
                ],
                'desc' => [
                    'en' => 'One Kilo is a supermarket delivery app built for fast reorders, fresh groceries, and dependable service for busy households.',
                    'ar' => 'ون كيلو تطبيق سوبر ماركت وتوصيل مقاضي مصمم لإعادة الطلب بسرعة وتوفير منتجات طازجة وخدمة يعتمد عليها للعائلات المشغولة.',
                ],
                'banner' => $defaultImage,
                'image' => $defaultImage,
            ]
        );

        $this->syncPageContent(
            Privacy::class,
            [
                'title' => [
                    'en' => 'Privacy Policy',
                    'ar' => 'سياسة الخصوصية',
                ],
                'desc' => [
                    'en' => 'We use customer data only to process orders, coordinate delivery, provide support, and improve the shopping experience.',
                    'ar' => 'نستخدم بيانات العملاء فقط لتنفيذ الطلبات وتنسيق التوصيل وتقديم الدعم وتحسين تجربة التسوق.',
                ],
                'banner' => $defaultImage,
                'image' => $defaultImage,
            ]
        );

        $this->syncPageContent(
            Terms::class,
            [
                'title' => [
                    'en' => 'Terms & Conditions',
                    'ar' => 'الشروط والأحكام',
                ],
                'desc' => [
                    'en' => 'By placing an order through One Kilo, you agree to our policies for delivery windows, item substitutions, returns, and payment confirmation.',
                    'ar' => 'عند إتمام طلب عبر ون كيلو فأنت توافق على سياسات مواعيد التوصيل واستبدال الأصناف والاسترجاع وتأكيد الدفع.',
                ],
                'banner' => $defaultImage,
                'image' => $defaultImage,
            ]
        );

        $this->syncFaqs([
            [
                'question' => [
                    'en' => 'How does grocery delivery work on One Kilo?',
                    'ar' => 'كيف يعمل توصيل المقاضي في ون كيلو؟',
                ],
                'answer' => [
                    'en' => 'Browse the catalog, add products to your cart, choose your address and delivery time, then confirm the order.',
                    'ar' => 'تصفح المنتجات ثم أضف احتياجاتك إلى السلة واختر العنوان وموعد التوصيل وبعدها أكمل تأكيد الطلب.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Can I schedule my order for later today?',
                    'ar' => 'هل يمكنني جدولة طلبي لوقت لاحق اليوم؟',
                ],
                'answer' => [
                    'en' => 'Yes. Available delivery windows depend on your area and the current order volume at checkout.',
                    'ar' => 'نعم. مواعيد التوصيل المتاحة تعتمد على منطقتك وحجم الطلبات الحالي عند إتمام الشراء.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'What happens if an item is out of stock?',
                    'ar' => 'ماذا يحدث إذا كان المنتج غير متوفر؟',
                ],
                'answer' => [
                    'en' => 'We can follow your substitution preferences or remove the item from the order before final billing.',
                    'ar' => 'يمكننا تطبيق بدائل حسب تفضيلاتك أو حذف المنتج من الطلب قبل الفاتورة النهائية.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'Is there a minimum order amount?',
                    'ar' => 'هل يوجد حد أدنى لقيمة الطلب؟',
                ],
                'answer' => [
                    'en' => 'The minimum order value may vary by delivery zone and active campaigns. The cart shows the current requirement.',
                    'ar' => 'قد يختلف الحد الأدنى للطلب حسب منطقة التوصيل والعروض الحالية، وستجد القيمة المطلوبة داخل السلة.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How are chilled and frozen products handled?',
                    'ar' => 'كيف يتم التعامل مع المنتجات المبردة والمجمدة؟',
                ],
                'answer' => [
                    'en' => 'Temperature-sensitive items are packed separately to help preserve product quality until delivery.',
                    'ar' => 'يتم تغليف المنتجات الحساسة للحرارة بشكل منفصل للمساعدة في الحفاظ على جودتها حتى وقت التوصيل.',
                ],
                'status' => 1,
            ],
            [
                'question' => [
                    'en' => 'How can I contact support about an order?',
                    'ar' => 'كيف أتواصل مع الدعم بخصوص طلب؟',
                ],
                'answer' => [
                    'en' => 'Reach us through the in-app contact form, support email, or WhatsApp number listed in the app settings.',
                    'ar' => 'يمكنك التواصل معنا من خلال نموذج التواصل داخل التطبيق أو بريد الدعم أو رقم واتساب الموجود في الإعدادات.',
                ],
                'status' => 1,
            ],
        ]);
    }

    private function syncPageContent(string $modelClass, array $attributes): void
    {
        $record = $modelClass::query()->firstOrNew([]);
        $record->fill($attributes);
        $record->save();
    }

    private function syncFaqs(array $faqs): void
    {
        $existingFaqs = Faq::query()
            ->get()
            ->keyBy(fn (Faq $faq) => $faq->getTranslation('question', 'en'));

        $legacyQuestions = [
            'How can I order from FIX Store?',
            'Do you sell original spare parts?',
            'How long does delivery take?',
            'Can merchants buy in bulk?',
            'What is your return policy?',
        ];

        foreach ($faqs as $faqData) {
            $englishQuestion = $faqData['question']['en'];

            $faq = $existingFaqs->get($englishQuestion, new Faq());
            $faq->fill($faqData);
            $faq->save();
        }

        $existingFaqs
            ->filter(fn (Faq $faq, string $question) => in_array($question, $legacyQuestions, true))
            ->each
            ->delete();
    }
}
