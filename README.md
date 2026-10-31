# newsletters

The newsletter listing provides **View** for a read-only preview of the saved
newsletter and **Edit** to open the configured editor. Save editor changes before
previewing them. The preview displays email HTML in a sandboxed frame.
The newsletter controller trait overrides the generic CRUD view action, so the
preview does not require an application-specific newsletter details form.



#### Inspiration

* https://help.salesforce.com/s/articleView?id=sf.communication_subscription_objects.htm&type=5