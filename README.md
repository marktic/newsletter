# newsletters

The newsletter listing provides **View** for a read-only preview of the saved
newsletter and **Edit** to open the configured editor. Save editor changes before
previewing them. The preview displays email HTML in a sandboxed frame.
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



#### Inspiration

* https://help.salesforce.com/s/articleView?id=sf.communication_subscription_objects.htm&type=5