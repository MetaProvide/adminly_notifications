app_name=

app_name=adminly_notifications
app_id=notifications
build_directory=$(CURDIR)/build
temp_build_directory=$(build_directory)/temp
build_tools_directory=$(CURDIR)/build/tools

all: dev-setup build-js-production

dev-setup: clean clean-dev npm-init

release: npm-init build-js-production build-tarball

# Dependencies
composer:
	composer install --prefer-dist

composer-update:
	composer update --prefer-dist

npm-init:
	npm ci

npm-update:
	npm update

# Building
build-js:
	npm run dev

build-js-production:
	npm run build

lint:
	npm run lint

lint-fix:
	npm run lint:fix

watch-js:
	npm run watch

clean:
	rm -f js/notifications.js
	rm -f js/notifications.js.map
	rm -rf $(build_dir)

clean-dev:
	rm -rf node_modules

build-tarball:
	rm -rf $(build_directory)
	mkdir -p $(temp_build_directory)
	rsync -a \
	--exclude=".git" \
	--exclude=".tx" \
	--exclude=".vscode" \
	--exclude="build" \
	--exclude="docs" \
	--exclude="node_modules" \
	--exclude="src" \
	--exclude="vendor" \
	--exclude=".eslintignore" \
	--exclude=".eslintrc.js" \
	--exclude=".gitattributes" \
	--exclude=".gitignore" \
	--exclude=".l10nignore" \
	--exclude=".php_cs.cache" \
	--exclude=".php-cs-fixer.dist.php" \
	--exclude="babel.config.js" \
	--exclude="composer.json" \
	--exclude="composer.lock" \
	--exclude="Makefile" \
	--exclude="package-lock.json" \
	--exclude="package.json" \
	--exclude="psalm.xml" \
	--exclude="stylelint.config.js" \
	--exclude="webpack.js" \
	../$(app_name)/ $(temp_build_directory)/$(app_id)
	tar czf $(build_directory)/$(app_name).tar.gz \
		-C $(temp_build_directory) $(app_id)
