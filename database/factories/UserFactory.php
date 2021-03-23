<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fname = $this->faker->firstName;
        $lname = $this->faker->lastName;
        $title = $this->faker->title;
        $username = strtolower($fname.'.'.substr($lname, 0, 3));
        $name = implode(' ', [$title, $fname, $lname]);
        $profile = [
            'login' => $username,
            'full_name' => $name,
            'full_name_en' => $name,
            'tel_no' => $this->faker->phoneNumber,
            'org_id' => $this->faker->numerify('100#####'),
            'remark' => '',
            'divisions' => [],
            'notification_channels' => [
              'provider' => 'log',
              'log' => [
                'id' => $username,
                'active' => true,
              ],
            ],
            'social' => [
              'provider' => 'log',
              'id' => $username,
              'name' => $username,
              'avatar' => '',
              'nickname' => $username,
            ],
        ];

        return [
            'name' => $username,
            'email' => $username.'@'.$this->faker->domainName,
            'profile' => $profile,
            'email_verified_at' => now(),
            'next_activation_at' => now()->addDays(30),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
