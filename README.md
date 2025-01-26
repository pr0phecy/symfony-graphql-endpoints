# PHP Backend Developer Test Project
## Project Setup
You can use the commands in the makefile to set up the project, just take a look

- make build
- make fixtures

## Explanation of choices

For the GraphQL endpoint part I have decided to use the existing ApiPlatform setup, to keep things simple.
For input validation I have decided to use Asserts in the entities, although I was tempted to use Dto's.
For applying the soft delete filter on entity.deletedAt I have decided to use an Extension, although I have also considered using a resolver.
For easier testing I have added data fixtures.
Tests are done using the dama doctrine test bundle. 
