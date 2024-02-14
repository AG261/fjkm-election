<?php

namespace App\Factory\Voting;

use App\Entity\Voting\Candidat;
use App\Repository\Voting\CandidatRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Candidat>
 *
 * @method        Candidat|Proxy create(array|callable $attributes = [])
 * @method static Candidat|Proxy createOne(array $attributes = [])
 * @method static Candidat|Proxy find(object|array|mixed $criteria)
 * @method static Candidat|Proxy findOrCreate(array $attributes)
 * @method static Candidat|Proxy first(string $sortedField = 'id')
 * @method static Candidat|Proxy last(string $sortedField = 'id')
 * @method static Candidat|Proxy random(array $attributes = [])
 * @method static Candidat|Proxy randomOrCreate(array $attributes = [])
 * @method static CandidatRepository|RepositoryProxy repository()
 * @method static Candidat[]|Proxy[] all()
 * @method static Candidat[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Candidat[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Candidat[]|Proxy[] findBy(array $attributes)
 * @method static Candidat[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Candidat[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class CandidatFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'birthday' => self::faker()->dateTime(),
            'civility' => self::faker()->randomElement(['Mr', 'Mme']),
            'firstname' => self::faker()->firstName(),
            'lastname' => self::faker()->lastname(),
            'photo' => "https://unsplash.com/photos/smiling-man-standing-near-green-trees-VVEwJJRRHgk",
            'status' => 1,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Candidat $candidat): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Candidat::class;
    }
}
