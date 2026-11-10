# newsletters

The newsletter listing provides **View** for a read-only preview of the saved
newsletter and **Edit** to open the configured editor. The view page also links
to the editor. Its **Edit HTML source** dialog can replace the complete saved
HTML and insert the selected definition's placeholders (by default
`{{ first_name }}`, `{{ last_name }}` and `{{ email }}`).
This package stores these tokens as-is; the host's sending integration must
replace them with recipient data. Save editor changes before previewing them.
The preview displays email HTML in a sandboxed frame.
The editor toolbar lets you rename the newsletter; **Save** persists the name
alongside the content and editor design in every supported editor.
Editor views are fragments embedded in the admin layout, not standalone documents.
GrapesJS includes icon-labeled basic blocks and ready-made image/text, header,
quote, social links and footer sections, plus a three-column layout.
The newsletter controller trait overrides the generic CRUD view action, so the
preview does not require an application-specific newsletter details form.

The **View** page shows the preview inline, with an **Open preview in new tab**
button (`preview` action, standalone page). The right column contains:

- **Filter cards** – one card per filter plus an *Add filter* form (`addFilter` /
  `deleteFilter` actions). Built-in types: `list`, `channel`, `subscribed_after`,
  `subscribed_before`. Register extra types (implementing `FilterTypeInterface`)
  in `filters.types`. Apply a newsletter's filters to a subscriptions query with
  `ApplyNewsletterFiltersToQuery::for($filters)->apply($query)`.
- **Send preview email** – sends the saved content to up to 10 addresses
  (`sendPreview` action). Configure `preview.sender` with a class/service
  implementing `PreviewEmailSenderInterface`; the default uses PHP `mail()`.

Host applications must route the `preview`, `addFilter`, `deleteFilter` and
`sendPreview` actions for the newsletters admin controller. The optional `send`
action accepts POST requests and delegates full sending to the definition.

### Newsletter definitions

Extend `Marktic\Newsletter\NewsletterDefinitions\BaseDefinition` to define the
filters, placeholders and sending behavior for a newsletter kind. Configure
classes by the newsletter's `owner` and `type`:

```php
'definitions' => [
    'default' => \Marktic\Newsletter\NewsletterDefinitions\BaseDefinition::class,
    'available' => [
        'organizations' => [
            'weekly' => \App\NewsletterDefinitions\WeeklyDefinition::class,
        ],
    ],
],
```

`DefinitionResolver::forNewsletter($newsletter)` instantiates the exact match,
or the default class when either key or the match is missing. Invalid definitions
raise an error rather than falling back. Each definition receives the newsletter
in its constructor and exposes it through `getNewsletter()`.

```php
class WeeklyDefinition extends \Marktic\Newsletter\NewsletterDefinitions\BaseDefinition
{
    public function getFilterTypes(): array
    {
        return \Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes::only(['list', 'channel']);
    }

    public function getPlaceholders(): array
    {
        return ['{{ organization }}' => 'Organization'];
    }
}
```

Override `sendPreview(mixed $recipients): array` to customize test sending and
return the recipient addresses; the base implementation uses the existing
preview action and configured sender. Override `send(): void` to implement full
delivery or enqueue it. The base method explicitly rejects full sending because
the package has no default bulk-delivery implementation. Definitions are
responsible for recipient selection, applying filters and resolving placeholders
when customizing delivery; the package does not automatically interpolate tokens.

The controller resolves one definition per newsletter, exposes it as
`newsletterDefinition` in the view/edit payloads, and uses it for the filter list,
editor placeholders and both sending actions. Direct filter creation also uses
the configured definition. Existing `newsletterFilterTypes()` controller
overrides remain supported.

### Available newsletter filters

Registering a filter type does not enable it for every newsletter. Definitions
select enabled filters through `getFilterTypes()`. `BaseDefinition` preserves
the existing filter configuration by the newsletter's `owner` and `type` fields:

```php
'filters' => [
    'types' => [], // Additional FilterTypeInterface classes.
    'default' => [],
    'available' => [
        'organizations' => [
            'weekly' => ['list', 'channel'],
            'announcement' => [],
        ],
    ],
],
```

An exact owner/type match replaces the default, including an explicit empty
list. Missing matches (or missing owner/type) use `filters.default`, which is
empty out of the box. To define the list in the newsletter controller instead,
override the trait hook:

```php
protected function newsletterFilterTypes($item): array
{
    return \Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes::only(['list']);
}
```

The controller hook takes precedence over the definition for both the form and
filter creation. Direct `CreateNewsletterFilter` calls use the definition unless given
`withFilterTypes(FilterTypes::only([...]))`. Unavailable types are rejected.
Existing saved filters remain readable and applicable even if no longer enabled.
Filter configuration lists are read as plain arrays, including when wrapped in
`Nip\Config\Config` by the host application's configuration.

#### Inspiration

* https://help.salesforce.com/s/articleView?id=sf.communication_subscription_objects.htm&type=5