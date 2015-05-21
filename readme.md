# Robot House

Mobile friendly wrapper app to control hand-picked 
devices on a Vera Lite

## todo

* timed refresh of everything. This should include refreshing the forwarding server at MIOS rather than taking it from ENV
* Gulp for assets
* Look at improving initial load. Best option is perhaps to separate data loading to a new endpoint, cache all data to local storage and accept that initial load will be stale. Advantages to this are that it links into timed refresh and paves the way for an in-memory cache on the proxy server to improve perceived performance further.