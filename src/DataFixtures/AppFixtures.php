<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserPreferences;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\MicroPost;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'john_doe',
            'email' => 'john_doe@doe.com',
            'password' => 'john123',
            'fullName' => 'John Doe',
            'roles' => [User::ROLE_USER],
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob_smith@smith.com',
            'password' => 'rob12345',
            'fullName' => 'Rob Smith',
            'roles' => [User::ROLE_USER],
        ],
        [
            'username' => 'marry_gold',
            'email' => 'marry_gold@gold.com',
            'password' => 'marry12345',
            'fullName' => 'Marry Gold',
            'roles' => [User::ROLE_USER],
        ],
        [
            'username' => 'vcaraseni',
            'email' => 'vcaraseni@gmail.com',
            'password' => 'Qwaszx12',
            'fullName' => 'Vladimir Caraseni',
            'roles' => [User::ROLE_ADMIN],
        ],
    ];

    private const POST_TEXT = [
        'Hello, how are you?',
        'It\'s nice sunny weather today',
        'I need to buy some ice cream!',
        'I wanna buy a new car',
        'There\'s a problem with my phone',
        'I need to go to the doctor',
        'What are you up to today?',
        'Did you watch the game yesterday?',
        'How was your day?'
    ];

    private const LANGUAGES = [
        'en',
        'fr'
    ];

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
	    $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     *
     * @throws \Exception
     */
    public function loadMicroPosts(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $microPost = new MicroPost();

            $microPost->setText(self::POST_TEXT[random_int(0, count(self::POST_TEXT) - 1)]);
	        $date = new \DateTime();
            $date->modify('-' . random_int(0, 10) . 'day');
    	    $microPost->setTime(new \DateTime('now'));
            $microPost->setUser($this->getReference(
	    	    self::USERS[random_int(0, count(self::USERS) - 1)]['username']
	        ));

            $manager->persist($microPost);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function loadUsers(ObjectManager $manager): void
    {
        foreach (self::USERS as $userData) {
            $user = new User();

            $user->setUserName($userData['username']);
            $user->setFullName($userData['fullName']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setPassword(
                $this->passwordEncoder->encodePassword($user,$userData['password'])
            );
            $user->setEnabled(true);
            $this->addReference($userData['username'], $user);

            $preferences = new UserPreferences();
            $preferences->setLocale(self::LANGUAGES[rand(0,1)]);

            $user->setPreferences($preferences);

            $manager->persist($user);
        }
        $manager->flush();
    }
}

