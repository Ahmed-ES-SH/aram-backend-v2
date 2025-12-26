<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            AboutSectionSeeder::class,
            AboutSeeder::class,
            SlideSeeder::class,
            CategorySeeder::class,
            ServiceCategorySeeder::class,
            CardCategorySeeder::class,
            ArticleCategorySeeder::class,
            TagSeeder::class,
            ArticleSeeder::class,
            ArticleInteractionSeeder::class,
            SubCategorySeeder::class,
            FooterListSeeder::class,
            KeyWordSeeder::class,
            PromoterSeeder::class,
            CardsTableSeeder::class,
            CurrencySeeder::class,
            PromotionActivitySeeder::class,
            CouponSeeder::class,
            PrivacyPolicySeeder::class,
            MemberSeeder::class,
            OrganizationSeeder::class,
            AppointmentSeeder::class,
            OfferSeeder::class,
            TermsConditionSeeder::class,
            OrganizatioPrivacyPolicySeeder::class,
            OrganizatioTermsConditionSeeder::class,
            OrganizationReviewSeeder::class,
            SocialContactInfoSeeder::class,
            FamilyMemberSeeder::class,
            QuestionAnswerSeeder::class,
            CommentSeeder::class,
            ContactMessagesSeeder::class,
            TransactionSeeder::class,
            ReferralSeeder::class,
            ServicePageSeeder::class,
            VariableDataSeeder::class,
        ]);
    }
}
