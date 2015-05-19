# Robot House

Mobile friendly wrapper app to control hand-picked 
devices on a Vera Lite

## todo

* Make a Dashboard class to display everything
* This will mean making other classes for the elements
* Make a storage class to lookup from server (and save to local?)
* That should have callbacks to update the dashboard
* Make client and server side code to run the scenes
* Make client side code to render the battery alerts

* Make server-side room code.
* Make client side code to render the room


* JS to render Dash
* JS to render a room
* JS to lookup and cache current room / scene states
    http://wiki.micasaverde.com/index.php/UI_Notes

    ### All devices:
    http://192.168.1.104:3480/data_request?id=lu_status2

    ### One device:
    http://192.168.1.104:3480/data_request?id=lu_status2&DeviceNum=28

* JS to handle changing a device
    - Make the call to Vera
    - Get the job details
    - Check the job progress
    - Update the device/cache on completion
* Hosting and domain access
* More I'm sure 