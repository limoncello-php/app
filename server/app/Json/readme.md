The overall process is

- HTTP request containing JSON API data is parsed and the data are validated with `Validators`.
- JSON API data are mapped to application models with `Schemes`.
- The converted input data are sent to API.

`Api` folder contains implementation for application API such as CRUD (Create, Read, Update and Delete) operations.

`Exceptions` folder contains a code that transforms various exceptions in API (application specific, authorization, 3rd party, etc) to JSON API errors.

`Schemes` folder contains descriptions for mapping between JSON API fields and application Model ones.

`Validators` folder contains validation rules for input JSON API data.
