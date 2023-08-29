<?php

use yii\helpers\Url;

?>
<!-- Create a video element -->
<video id="video-player" controls width="640" height="360">
</video>
<!-- Initialize the video player -->
<script>
    // Wait for the document to be ready before initializing video.js
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize the video.js player
        var video = document.getElementById("video-player");
        var source = "<?php echo Url::toRoute('stream/'); ?>/PC.m3u8"
        const defaultOptions = {};
        if(Hls.isSupported()){
            const hls = new Hls();
            hls.loadSource(source);
            hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                const availableQualities = hls.levels.map((l) => l.height)
                defaultOptions.controls = [
                    'play-large', // The large play button in the center
                    'restart', // Restart playback
                    'rewind', // Rewind by the seek time (default 10 seconds)
                    'play', // Play/pause playback
                    'fast-forward', // Fast forward by the seek time (default 10 seconds)
                    'progress', // The progress bar and scrubber for playback and buffering
                    'current-time', // The current time of playback
                    'duration', // The full duration of the media
                    'mute', // Toggle mute
                    'volume', // Volume control
                    'captions', // Toggle captions
                    'settings', // Settings menu
                    'pip', // Picture-in-picture (currently Safari only)
                    'airplay', // Airplay (currently Safari only)
                    'fullscreen', // Toggle fullscreen
                ]
            
                defaultOptions.quality = {
                    default: availableQualities[0],
                    options: availableQualities,
                    forced: true,
                    onChange: e => updateQuality(e)
                }
                new Plyr(video, defaultOptions);
            });
            hls.attachMedia(video);
            window.hls = hls
        }
        function updateQuality(newQuality){
            window.hls.levels.forEach((level, levelIndex) => {
                if(level.height === newQuality){
                    window.hls.currentLevel = levelIndex
                }
            });
        }
    });

    // Disable access to developer tools
    function detectDevTools() {
        // Redirect or show an alert message to inform users
        // window.location.href = '<?php echo Url::toRoute('video/error'); ?>';
    }

	// Check for browser extensions that may download videos
	function detectDownloadExtensions() {
            const extensions = ['Video Downloader', 'SaveFrom.net', 'Video DownloadHelper'];
            const activeExtensions = extensions.filter(ext => window.navigator.userAgent.includes(ext));

            if (activeExtensions.length > 0) {
        		window.location.href = '<?php echo Url::toRoute('video/error'); ?>';
            }
        }

	// Check for download extensions after the page is fully loaded
	window.addEventListener('load', detectDownloadExtensions);

	function detectVideoDownloaderExtensions() {
		let videoDownloaderExtensions = ['Video Downloader professional', 'Video DownloadHelper', 'Flash Video Downloader'];
		let detectedExtensions = [];
		chrome.management.getAll(function (extensions) {
			for (let i = 0; i < extensions.length; i++) {
				if (videoDownloaderExtensions.includes(extensions[i].name) && extensions[i].enabled) {
					detectedExtensions.push(extensions[i].name);
				}
			}
			console.log('Detected video downloader extensions: ', detectedExtensions);
		});
	}

	detectVideoDownloaderExtensions();


/// <reference types="./index.d.ts"/>
/** @typedef {{ moreDebugs: number }} PulseCall */
/** @typedef {{ isOpenBeat: boolean }} PulseAck */

(() => {
	/** @type {DevtoolsDetectorConfig} */
	const config = {
		pollingIntervalSeconds: 0.25,
		maxMillisBeforeAckWhenClosed: 100,
		moreAnnoyingDebuggerStatements: 1,

		onDetectOpen: detectDevTools,
		onDetectClose: undefined,

		startup: "asap",
		onCheckOpennessWhilePaused: "returnStaleValue",
	};
	Object.seal(config);

	const heart = new Worker(URL.createObjectURL(new Blob([
// Note: putting everything before the first debugger on the same line as the
// opening callback brace prevents a user from placing their own debugger on
// a line before the first debugger and taking control in that way.
`"use strict";
onmessage = (ev) => { postMessage({isOpenBeat:true});
	debugger; for (let i = 0; i < ev.data.moreDebugs; i++) { debugger; }
	postMessage({isOpenBeat:false});
};`
	], { type: "text/javascript" })));

	let _isDevtoolsOpen = false;
	let _isDetectorPaused = true;

	// @ts-expect-error
	// note: leverages that promises can only resolve once.
	/**@type {function (boolean | null): void}*/ let resolveVerdict = undefined;
	/**@type {number}*/ let nextPulse$ = NaN;

	const onHeartMsg = (/** @type {MessageEvent<PulseAck>}*/ msg) => {
		if (msg.data.isOpenBeat) {
			/** @type {Promise<boolean | null>} */
			let p = new Promise((_resolveVerdict) => {
				resolveVerdict = _resolveVerdict;
				let wait$ = setTimeout(
					() => { wait$ = NaN; resolveVerdict(true); },
					config.maxMillisBeforeAckWhenClosed + 1,
				);
			});
			p.then((verdict) => {
				if (verdict === null) return;
				if (verdict !== _isDevtoolsOpen) {
					_isDevtoolsOpen = verdict;
					const cb = { true: config.onDetectOpen, false: config.onDetectClose }[verdict+""];
					if (cb) cb();
				}
				nextPulse$ = setTimeout(
					() => { nextPulse$ = NaN; doOnePulse(); },
					config.pollingIntervalSeconds * 1000,
				);
			});
		} else {
			resolveVerdict(false);
		}
	};

	const doOnePulse = () => {
		heart.postMessage({ moreDebugs: config.moreAnnoyingDebuggerStatements });
	}

	/** @type {DevtoolsDetector} */
	const detector = {
		config,
		get isOpen() {
			if (_isDetectorPaused && config.onCheckOpennessWhilePaused === "throw") {
				throw new Error("`onCheckOpennessWhilePaused` is set to `\"throw\"`.")
			}
			return _isDevtoolsOpen;
		},
		get paused() { return _isDetectorPaused; },
		set paused(pause) {
			// Note: a simpler implementation is to skip updating results in the
			// ack callback. The current implementation conserves resources when
			// paused.
			if (_isDetectorPaused === pause) { return; }
			_isDetectorPaused = pause;
			if (pause) {
				heart.removeEventListener("message", onHeartMsg);
				clearTimeout(nextPulse$); nextPulse$ = NaN;
				resolveVerdict(null);
			} else {
				heart.addEventListener("message", onHeartMsg);
				doOnePulse();
			}
		}
	};
	Object.freeze(detector);
	// @ts-expect-error
	globalThis.devtoolsDetector = detector;

	switch (config.startup) {
		case "manual": break;
		case "asap": detector.paused = false; break;
		case "domContentLoaded": {
			if (document.readyState !== "loading") {
				detector.paused = false;
			} else {
				document.addEventListener("DOMContentLoaded", (ev) => {
					detector.paused = false;
				}, { once: true });
			}
			break;
		}
	}
})();
</script>




