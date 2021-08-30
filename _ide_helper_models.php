<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Answer
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer query()
 */
	class Answer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExpertTest
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertTest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertTest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExpertTest query()
 */
	class ExpertTest extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Question
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Test
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @method static \Illuminate\Database\Eloquent\Builder|Test newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Test newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Test query()
 */
	class Test extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TestCategory
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @method static \Illuminate\Database\Eloquent\Builder|TestCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TestCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TestCategory query()
 */
	class TestCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TestResult
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @method static \Illuminate\Database\Eloquent\Builder|TestResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TestResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TestResult query()
 */
	class TestResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-write mixed $password
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

