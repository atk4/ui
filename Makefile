.PHONY: docs tests
docs:
	cd docs && make html
tests:
	phpunit
