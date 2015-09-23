# Database Schema Updates

LibreNMS structures SQL-Schema updates in an incremental fashion on a per-component basis.

To avoid collisions you must keep some basic policies in mind:
- Components must have unique names and may not alter tables from other components.
- Components should not be renamed once they are upstream.
- Each file must be a numeric increment of the previous schema, starting with `1.sql`. Padding zeroes are allowed but not required.

LibreNMS Schema-Components follow the Java Naming Convention of `$tld.$domain.$component[.$subcomponent]`.  

If your contribution is on behalf an organization, it's advised to use the organization's main domain.  
However if you contribute as single person, use your preferred email address instead substituting the `@` with a dot.

For example a component called `foobar` coded by the organization 'Awesome Ltd' with the domain `aweso.me` would be called `me.aweso.foobar`.  
Whereas the same component developed by John.Doe@gmail.com would be `com.gmail.john.doe.foobar`.
