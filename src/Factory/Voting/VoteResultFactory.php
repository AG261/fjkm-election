<?php

namespace App\Factory\Voting;

use App\Entity\Voting\VoteResult;
use App\Repository\Voting\VoteResultRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<VoteResult>
 *
 * @method        VoteResult|Proxy create(array|callable $attributes = [])
 * @method static VoteResult|Proxy createOne(array $attributes = [])
 * @method static VoteResult|Proxy find(object|array|mixed $criteria)
 * @method static VoteResult|Proxy findOrCreate(array $attributes)
 * @method static VoteResult|Proxy first(string $sortedField = 'id')
 * @method static VoteResult|Proxy last(string $sortedField = 'id')
 * @method static VoteResult|Proxy random(array $attributes = [])
 * @method static VoteResult|Proxy randomOrCreate(array $attributes = [])
 * @method static VoteResultRepository|RepositoryProxy repository()
 * @method static VoteResult[]|Proxy[] all()
 * @method static VoteResult[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static VoteResult[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static VoteResult[]|Proxy[] findBy(array $attributes)
 * @method static VoteResult[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static VoteResult[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class VoteResultFactory extends ModelFactory
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
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(VoteResult $voteResult): void {})
        ;
    }

    protected static function getClass(): string
    {
        return VoteResult::class;
    }
}
