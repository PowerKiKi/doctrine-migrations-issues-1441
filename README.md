# Doctrine migrations issue #1441

In https://github.com/doctrine/migrations/issues/1441, we talk about the fact that the removal of support for column
comments, in https://github.com/doctrine/dbal/pull/5107, broke the use-case of enums (and sets) in the database.

Assuming we want:

- Latest ORM and DBAL, so 3.2 and 4.0 at the time of writing
- Real enum in database (not varchar)
- Migrations that are not always re-generated
- Scalability for multiple enums, by avoiding boilerplate and duplicated code/values

This repository demonstrates all known approaches and shows that none of them can fulfill our goals since DBAL 4.0.0,
because none of the unit tests in `tests/AllTest.php` are passing.
