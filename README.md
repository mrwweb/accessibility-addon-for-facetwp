# Accessible FacetWP with real inputs and labels

Mark Root-Wiley, [MRW Web Design](https://MRWweb.com)

This plugin replaces many of the basic facets in FacetWP with fully-accessible HTML inputs and labels. This leads to better accessibility and easier styling. It's unlikely it will ever have full-coverage of facets, but it's a great potential choice if you are only using simple facets like search and a flat list of checkboxes.

## Unaffiliated with FacetWP

This plugin is not affiliated with the FacetWP plugin in any way. FacetWP is a great plugin, and hopefully this makes it even better!

## An alternative to the FacetWP "a11y support"

This plugin is an alternative to the FacetWP "a11y support" feature / `facetwp_load_a11y` filter and they should not be used together. It should prevent a11y support script from loading when activated.

## Currently Supported Facets

### Taxonomy / Checkboxes (Partial facet support)

- Uses `<input type="checkbox">` and `<label>` with associated `id`/`for` attributes
- Wraps the list of checkboxes in a `<fieldset>` with a `<legend>` for the facet title
- Limitations:
  - Works for a "flat" list of checkboxes without expand/collapse or hierarchical nesting
  - Doesn't support ghost facets (I think?)

### Search

- Wraps field in `<search>` element
- Labels the field
- Removes `<i>` element for search button and replaces with a real `<button>`.
  - The button's text is wrapped in a span in case you want to accessibly hide the text and show an icon with a pseudo-element. We could potentially add an action/filter to make this more configurable in the future.

### Pager

- Wraps pager in a `<nav>` element labeled by an associated `<h3>` heading
  - Heading level assumes the Facet Results are labeled by an `<h2>`
- Converts consecutive link elements into an unordered list of links
- Adds tabindex to valid links to enable keyboard navigation

### Sort

- Adds `<label>` with appropriate `for` and `id` attributes to the select element

## Roadmap / Ideas / Known Issues

- Is it possible to render `aria-live="polite"` around the results count? (Markup must be in page source on load to work with all screen readers)
- Adjust radio buttons to match checkbox markup
- Add _basic_ support for "Select" facets similar to existing "Sort" facet fix
- Make hard-coded labels and headings filterable
- Add an index or other unique key to ensure added `for`/`id` attributes are unique when a facet is added to the page more than once
- Add support for ghost facets (probably with some use of `disabled` but [beware of the drawbacks](https://adrianroselli.com/2024/02/dont-disable-form-controls.html))
