<?php

namespace App\Factory\Voting;

use App\Entity\Voting\Vote;
use App\Factory\Account\UserFactory;
use App\Repository\Voting\VoteRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Vote>
 *
 * @method        Vote|Proxy create(array|callable $attributes = [])
 * @method static Vote|Proxy createOne(array $attributes = [])
 * @method static Vote|Proxy find(object|array|mixed $criteria)
 * @method static Vote|Proxy findOrCreate(array $attributes)
 * @method static Vote|Proxy first(string $sortedField = 'id')
 * @method static Vote|Proxy last(string $sortedField = 'id')
 * @method static Vote|Proxy random(array $attributes = [])
 * @method static Vote|Proxy randomOrCreate(array $attributes = [])
 * @method static VoteRepository|RepositoryProxy repository()
 * @method static Vote[]|Proxy[] all()
 * @method static Vote[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Vote[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Vote[]|Proxy[] findBy(array $attributes)
 * @method static Vote[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Vote[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class VoteFactory extends ModelFactory
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
            'created' => self::faker()->dateTime(),
            'num' => self::faker()->text(255),
            'updated' => self::faker()->dateTime(),
            'user' => UserFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Vote $vote): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Vote::class;
    }
}
