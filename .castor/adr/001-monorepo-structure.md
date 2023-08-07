ADR 001 - Monorepo Structure
============================

## Status
- Proposal (Aug 5th, 2023)

## Sponsor
- Matias Navarro-Carter

## Context

Setting to build a standard library in PHP immediately poses the question: how will this library be shipped to the
end users? There is no question about this library being shipped using composer (there is really no other alternative
in modern PHP). 

It's also clear there this standard library MUST be shipped as multiple packages. This because having a single package
that contains everything will really harm adoption, as most people will like the option to select the components
they want to work with on an individual basis. If we don't provide this ability, people simply won't be wiling to
pull such a big dependency into their projects.

However, as the development team, we don't really want to be in a position of having to maintain all of these packages
in separate repositories, since it is more time consuming and there is a lot of boilerplate code that will be reused.

## Decision

We will adopt a monorepo structure to maintain these packages. All of the packages will live in the same repository.

We will use git's subtree splitting features to mirror every package into a repository of their own.

We will set up composer's packages for each of those repositories.

We will invest in tooling and automation to make sure development experience, subtree linking and versioning are all
managed properly.

## Consequences

This will allow us to maintain a consistent and agile development experience for our ecosystem of packages, at the same
time of providing the ability for people to require only the components they need.

However, mono-repos bring significant challenges with regards to versioning and releasing that we would like to address
properly. For instance, we would like to comply with Semantic Versioning, and we would also like every package to be
in control or their own version. This opposed to what other famous monorepo do, like Symfony, in which all the packages
are released with the same version.

Lastly, automation tooling will need to be specified, implemented and maintained.