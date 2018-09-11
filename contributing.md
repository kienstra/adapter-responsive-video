#  Contributing To Adapter Post Preview

To clone the repository:
``` bash
$ git clone --recursive git@github.com:kienstra/adapter-responsive-video.git
```

## Installing The Pre-Commit Hook
The submodule [wp-dev-lib](https://github.com/xwp/wp-dev-lib#install-as-submodule) has a pre-commit hook that will check compliance with PHPCS, and run the unit tests. As its [repo recommends](https://github.com/xwp/wp-dev-lib#install-as-submodule), run:
``` bash
$ ./dev-lib/install-pre-commit-hook.sh
```

## Running PHPUnit Tests

This requires an environment with WordPress unit tests, such as [VVV](https://github.com/Varying-Vagrant-Vagrants/VVV).

Run tests:

``` bash
$ phpunit
```

Run tests with coverage:

``` bash
$ phpunit --coverage-html /tmp/report
```

## Branching Strategy: [GitHub Flow](https://guides.github.com/introduction/flow/)
1. Branch off the [develop branch](https://github.com/kienstra/adapter-responsive-video/tree/develop)
2. Open a pull request to that branch
3. Merge that pull request when the Travis builds and code review pass
4. Create a release branch off [develop](https://github.com/kienstra/adapter-responsive-video/tree/develop) when the release is ready
5. Open a pull request to [master](https://github.com/kienstra/adapter-responsive-video/tree/master), review it when the Travis builds pass, and merge it
6. Deploy the [master branch](https://github.com/kienstra/adapter-responsive-video/tree/master) to [WordPress.org](https://wordpress.org/plugins/adapter-responsive-video/)
