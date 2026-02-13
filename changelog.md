### v2.0.1 
* Add unit tests for theme commands and fix generation bugs
* Add unit tests for database module commands
* Add unit test for CrudMakeCommand and register APICrudMakeCommand
* feat(dev-tool): Add options to publish only skills or rules agent subfolders and update documentation
* docs(dev-tool): add documentation for the new `agents:publish` command.
* Fix ResourceMakeCommand test failure in CI
* Refactor model handling in ResourceMakeCommand
* Add unit tests for module generator and management commands
* Remove ShouldQueue interface from job stub class
* Implement ShouldQueue interface in job stub
* Add unit tests for module generator and management commands
* Add unit tests for ModuleMakeCommand
* Add unit test for ListenerMakeCommand and fix event namespace bug
* Add unit test for JobMakeCommand and fix job.stub
* Add unit test for ModelShowCommand
* Add unit test for NotificationMakeCommand
* Add unit test for ModelMakeCommand
* Add unit test for ModuleDeleteCommand
* Add unit test for MailMakeCommand
* Add unit test for DatatableMakeCommand
* Add unit test for EventMakeCommand
* Add unit test for MiddlewareMakeCommand
* feat: add jules-environment-setup.sh for environment setup with PHP and Node.js
* chore: Add `setup.sh` to `.gitignore` stubs for modules and themes.
* feat(dev-tool): add `agents:publish` command and a comprehensive code style guide.
* Fix CommandMakeCommandTest and add ComponentClassMakeCommandTest
* Add unit test for ComponentClassMakeCommand
* config: Separate Unit and Feature test suites in phpunit.xml.
* refactor(tests): move common package providers and aliases from `CommandMakeCommandTest` to base `TestCase`.
* chore(composer): move juzaweb/core from `require` to `require-dev` dependencies.
* feat(ci): add GitHub Actions workflow to run tests across PHP versions.
* Add unit test for CommandMakeCommand
* docs: Add detailed README
* feat: initialize testing environment
* refactor(config): remove module, assets, and migration path configurations from dev-tool.php.
* chore(dev-tool): add .gitignore to exclude common development files.
* chore(dev-tool): remove readme.md file
* ♻️ refactor(CommandMakeCommand): Remove unused module variable from getDefaultNamespace method
* refactor(dev-tool): update module generator commands to use global config helper for path configurations
* refactor(ModuleGenerator): make constructor parameters nullable and use global config helper for module settings.
* refactor(dev-tool): update type hints, standardize config access in ModuleGenerator, and correct stub path configuration.
* feat(dev-tool): add composer configuration and correct module generation config paths
* ♻️ refactor(ConsoleServiceProvider): Remove unused module commands for cleaner command registration
* feat(dev-tool): introduce base and concrete generator classes and update stub path configurations for dev-tool commands.
* ♻️ refactor(dev-tool): Update theme stubs path and add module stubs configuration
* ♻️ refactor(composer, dev-tool): Add Juzaweb core dependency and theme stubs configuration
* ✨ feat(themes): Add theme management commands for listing, activating, seeding, and installing themes
* ♻️ refactor: Move command classes to Modules namespace for better organization
* ✨ feat: Add various stub files for module scaffolding and initial setup

### v2.0.0 
* refactor(dev-tool): enable module path specification for github release command and merge dev-tool config.
* feat(dev-tool): register GithubReleaseModuleCommand.
* feat(dev-tool): allow GitHub token to be configured via environment variable.
* tada: Begin v2
* :+1: Add readme make plugin
* :memo: Update readme
* :+1: Preview release
* :+1: Add theme route to plugin
* :+1: Move translation to plugin
* :+1: Make block command
* :+1: Clear logs command

### v1.0.7 
* :+1: Download style template

### v1.0.6 
* :bug: Fix resource stub
* :+1: Make Demo Content Command

### v1.0.5 
* :+1: Make Demo Content Command

### v1.0.4 
* :+1: Add changelog before release
* :bug: Fix get last tag
* :bug: Fix get last tags release github
* :+1: Fix dev tool
* :+1: Update stub template
* :bug: Fix make block theme command

### v1.0.3 
* :bug: Fix make theme command
* :bug: Fix ci/cd
* :memo: Update changelog

### v1.0.2 
* :+1: Truck make commands
* :+1: Module select make commands
* :+1: Filter construction release command
* :bug: Fix release empty last tag
* :+1: Theme active command
* :+1: GitHub release command

