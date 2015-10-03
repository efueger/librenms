Using LibreNMS Daemon
---------------------

## Abstract

LibreNMS consists of several independent jobs that can be run either as cronjob or using the shipped daemon `librenmsd`.

Cronjobs often have a penalty in accurancy so when adding a fixed interval in an alert, it might be skipped because be cronjob triggered the job some secs earlier than in the previous cycle. Although this can be countered by adjusting the Alert's tolerance-window, it's generally not a good thing to have jobs triggered at irregular cycles when the backend expects it to be as accurate and regular as possible.

As mentioned with the Alerts, also RRD has a fixed step of 5 minutes and a tolerance of about +/-10 secs.

Irregularities within your RRDs may have a more severe impact on the NMS as it may lead to invalid datasets and thus be ignored in graphing. This is bad.

These reasons, although not exclusively, made the need of an accurate daemon to replace the cron-daemon in order to gain precise cycles.

The future goal will be to have a set of self-managed and auto-balanced daemons that schedule their job-object among eachother automagically. Ideally with backfeeding and processing tasks to aggregate collection and interpretation of data off-pollers.

## Running the Daemon

### Configuration

The daemon expects the file `/etc/librenmsd.ini` to be readable for the LibreNMS user and to contain at least:

```text
BASEDIR = "/opt/librenms"
```

Otherwise it will not attempt to start and ask you to create that file.

### Running it as init-script

On most systems, you can simply symlink `/etc/init.d/librenmd` to `/opt/librenms/librenmsd`.
The daemon comes with LSB Compliant headers, on a debian system you would issue `insserv librenmsd` or similar to autogenerate the runlevel links.

On a RHEL/Centos system prior to 7, you need to issue `chkconfig --add librenmsd`.

On a distribution using systemd (RHEL/Centos 7 or later) you'll need to create a `librenmsd.service` file yourself and put it in the correct directory.
Here is a skelleton:
```systemd
[Unit]
Description=LibreNMS Daemon
After=syslog.target

[Service]
ExecStart=/opt/librenmsd foreground

[Install]
WantedBy=multi-user.target
```

### Running it through cron

You can run the daemon through cron to have watchdog capabilities. This way the cron will attempt to restart the daemon in case it exists unexpectedly.

```crontab
*    *    * * *   root    /opt/librenms/librenmsd start >> /dev/null 2>&1
```

## Job configuration

The Daemon reloads the `config.php` on changes.

Currently each interval is 10s. Although it is possible to go lower, it's discouraged to do so in order to avoid unecessarly loads.

The config auto-detects when Distributed Polling is set up and will automatically exclude jobs for alerts, services and billing unless told not to in order to avoid redundant work across the pollers. See `Notes on Distributed Polling` for cofig options.


### Job Object

Each Job is an array in form of `array('type'=>%TYPE, 'file'=>%FILE, 'args'=>%ARGS)`.

`%TYPE` can be either `include`, `internal` or `exec`. If set to `include`, the daemon will include the file specified. If set to `exec` it will execute the file within a subshell.
The type `internal` is reserved for internal functions within the basecode itself, usually for distribution wrappers.

`%FILE` is relative to the install-directory specified in the `config.php`.

`%ARGS` is optional and will only be passed to jobs of type `exec`.

### Intervals Object

The Intervals Object is an 3 dimensional array. Each dimension is a positive integer.

| Dimension | Usage                 |
| --------- | --------------------- |
| 1         | Base in seconds.      |
| 2         | Units of Base.        |
| 3         | Order to execute Job. |

In other words, if you want to run a job every 7 minutes, use `[60][7][] = $MyJob;`.
As you can see, the last dimension is empty. Unless you know what you're doing, there's no reason to specify it, it's more likely that you will overwrite a previous job by accident.

Although you can define each job by the base-unit of 1, we discourage this. It's more effective if defaulting to the next highest base-unit.

Defaults:
```php
  $config['daemon']['intervals'][60][2][]    = array('type'=>'internal', 'func'=>'discovery', 'args'=>array('threads'=>'16','new'=>'1')); // Discover new devices every 2 minutes
  $config['daemon']['intervals'][60][5][]    = array('type'=>'internal', 'func'=>'poller',    'args'=>array('threads'=>'16'));            // Poller runs every 5 minutes
  $config['daemon']['intervals'][3600][6][]  = array('type'=>'internal', 'func'=>'discovery', 'args'=>array('threads'=>'16'));            // Re-Discover every 6 Hours
  $config['daemon']['intervals'][86400][1][] = array('type'=>'exec',     'file'=>'daily.sh');                                             // Daily at midnight.
```

#### Notes on Threads

Poller and Discovery accept a `threads` parameter, this can be any desired positive integer number. Please keep in mind that this `thread` parameter only account worker threads and not the process manager and other parallelized jobs.

The Poller accepts an additional value to the `threads` parameter, called `auto`.

##### Automatically (Re)Setting Threads

Jobs that allow `auto` as value for the `threads` parameter also accept the following parameters to fine tune and/or limit the auto-threading capabilities:

- `min` Minimum amount of Threads to spawn. Defaults to `2`.
- `max` Maximum amount of Threads to spawn. Defaults to `16`.
- `thresh` Threshold of Threads to begin scaling. Note: Down-Scaling happens on `thresh * 1.5`. The daemon will not re-adjust the threads unless the difference between current and new thread amount is larger or equal than the `thresh` parameter. Defaults to `1`.
- `target` Desired total amount of polling time. This is the basis used to calculate the thread amount. On a very vivid environment where device additions are very common it's suggested to keep this around 120 secs to allow enough space for additional hosts to be polled for the first time. Defaults to `150`.

## Notes on Distributed Polling

When running in a distributed setup, the default behavior is to exclude the non-distributable jobs for _Dispatching Alerts_, _Calculating Bills_ and _Check Services_.

It's recommended to only allow one poller to execute those 3 jobs, best would be to let the GUI-Machine do it.

Defaults:
```php
  $config['daemon']['run']['alerts']   = true; // Run alerts   eventhough in a distributed setup
  $config['daemon']['run']['billing']  = true; // ..  billing  ..
  $config['daemon']['run']['services'] = true; // ..  services ..
```

## Daemon Config

The Daemon will log to your syslog. The default Facility is `LOG_DAEMON`, Debug-Statements will go to `LOG_DEBUG` regardless of the `facility`-settings.


Defaults:
```php
  $config['daemon']['facility'] = LOG_DAEMON;                        // Syslog-facility.
  $config['daemon']['debug']    = false;                             // Debug, General Enable/Disable (true/false) or Enable specific sections by names.
  $config['daemon']['uid']      = posix_getpwnam('librenms')['uid']; // UID to use for daemon. Here it attempts to autodetect it from the `librenms` user.
  $config['daemon']['gid']      = posix_getpwnam('librenms')['gid']; // GID to use for daemon. Here it attempts to autodetect it from the `librenms` user.
```

## Debugging!

#### Run in foreground

```shell
/opt/librenms/librenmsd foreground
```

#### Add a sections to debug

`$config['daemon']['debug'] = 'jobctl,clock,main';`

#### Transform Interval to Time

`rTimestamp  = ( Interval * Step )`

This returns the number of seconds from 00:00:00.

#### How to calculate from Dimensions to Trigger

`Trigger     = ( Base * Units ) / Step`

A Job triggers when `Interval % Trigger` equals `0`

