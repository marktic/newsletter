# newsletters

The newsletter listing provides **View** for a read-only preview of the saved
newsletter and **Edit** to open the configured editor. Save editor changes before
previewing them. The preview displays email HTML in a sandboxed frame.
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
`sendPreview` actions for the newsletters admin controller.

### Available newsletter filters

Registering a filter type does not enable it for every newsletter. Configure
enabled filter names by the newsletter's `owner` and `type` fields:

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

The controller definition takes precedence over config for both the form and
filter creation. Direct `CreateNewsletterFilter` calls use config unless given
`withFilterTypes(FilterTypes::only([...]))`. Unavailable types are rejected.
Existing saved filters remain readable and applicable even if no longer enabled.
Filter configuration lists are read as plain arrays, including when wrapped in
`Nip\Config\Config` by the host application's configuration.

#### Inspiration

* https://help.salesforce.com/s/articleView?id=sf.communication_subscription_objects.htm&type=5