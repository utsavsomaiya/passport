<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\FieldType;
use App\Models\Attribute;
use App\Models\Template;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fashion Attribute - Ref: https://www.notion.so/Templates-d9d38f8be1cb484ea364f816ec168c8e?pvs=4#a695f4c5c0d542c488e56c471523ae31
        $fashion = Template::firstWhere('name', 'Fashion');
        Attribute::factory()->for($fashion)->create([
            'name' => 'Brand',
            'description' => 'This is for choose the brand of the items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Nike', "Levi's", 'Ralph Lauren', 'Tommy Hilfiger', 'Calvin Klein'],
            'is_required' => true,
            'status' => true,
        ]);

        // Shirt Attributes
        $shirt = Template::firstWhere('name', 'Shirt');
        Attribute::factory()->for($shirt)->create([
            'name' => 'Fit',
            'description' => 'This is choose for the shirt items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Slim', 'Regular', 'Athletic', 'Relaxes', 'Oversized'],
            'is_required' => true,
            'status' => true,
        ]);
        Attribute::factory()->for($shirt)->create([
            'name' => 'Material',
            'description' => 'This is choose for the shirt items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Cotton', 'Linen', 'Polyester', 'Silk', 'Rayon'],
            'is_required' => true,
            'status' => true,
        ]);
        Attribute::factory()->for($shirt)->create([
            'name' => 'Sleeve Length',
            'description' => 'This is choose for the shirt items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Short', 'Long', 'Three-Quarter', 'Roll-Up', 'Cap'],
            'is_required' => true,
            'status' => true,
        ]);

        // Jeans Attributes
        $jeans = Template::firstWhere('name', 'Jeans');
        Attribute::factory()->for($jeans)->create([
            'name' => 'Fit',
            'description' => 'This is choose for the jeans items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Slim', 'Straight', 'Skinny', 'Relaxed', 'Bootcut'],
            'is_required' => true,
            'status' => true,
        ]);
        Attribute::factory()->for($jeans)->create([
            'name' => 'Length',
            'description' => 'This is choose for the jeans items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Short', 'Regular', 'Long', 'Extra-Long'],
            'is_required' => true,
            'status' => true,
        ]);
        Attribute::factory()->for($jeans)->create([
            'name' => 'Number of pockets',
            'description' => 'This is choose for the jeans items',
            'field_type' => $fieldType = FieldType::NUMBER,
            'is_required' => true,
            'status' => true,
        ]);

        $sneakers = Template::firstWhere('name', 'Sneakers');
        Attribute::factory()->for($sneakers)->create([
            'name' => 'Material',
            'description' => 'This is choose for the sneakers items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Leather', 'Mesh', 'Canvas', 'Synthetic', 'Knit'],
            'is_required' => true,
            'status' => true,
        ]);
        Attribute::factory()->for($sneakers)->create([
            'name' => 'Toe Shape',
            'description' => 'This is choose for the sneakers items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Round', 'Pointed', 'Square', 'Almond', 'Cap'],
            'is_required' => true,
            'status' => true,
        ]);

        $wallets = Template::firstWhere('name', 'Wallets');
        Attribute::factory()->for($wallets)->create([
            'name' => 'Type',
            'description' => 'This is choose for the wallets items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Bifold', 'Trifold', 'Cardholder', 'Money Clip', 'Zip-around'],
            'is_required' => true,
            'status' => true,
        ]);
        Attribute::factory()->for($wallets)->create([
            'name' => 'Material',
            'description' => 'This is choose for the wallets items',
            'field_type' => $fieldType = FieldType::SELECT,
            'options' => ['Leather', 'Suede', 'Fabric', 'Nylon', 'Metal'],
            'is_required' => true,
            'status' => true,
        ]);
        Attribute::factory()->for($wallets)->create([
            'name' => 'Water Resistant',
            'description' => 'This is choose for the wallets items',
            'field_type' => $fieldType = FieldType::TOGGLE,
            'is_required' => true,
            'status' => true,
        ]);
    }
}
