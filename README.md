# Moodle Wowza Connector (MoWoCo)

This is a filter for Moodle 2+. It expands the built-in mediaplugin to connect to an external Wowza streaming server. It takes advantage of the different type of streaming, which Wowza streaming server supports. Currently it support Desktop computer by Flash and iOS devices by HTML5. 


## Installation

Put all the files in a own directory(e.g. mowoco) under filter in your moodle installation.

<pre><code>cd moodle/filter  
mkdir wowza  
cd wowza</code></pre>
#Here you have to place the files

## Setup

Activate the Wowza Connector.

Go there: Moodle website administration > plugins > filter > overview  
Look for a filter named Wowza Connector and switch the state option to 'on'

## How to use the filter

### Basic:

'<a href="rtmp://streaming.server.de/vod/myvideo.mp4">'
This is the normal syntax to embed a media file in moodle.  
IMPORTANT: To use this filter you must use the rtmp protokol and a MP4 video.  

### Embed with defining the video size

'<a href="rtmp://streaming.server.de/vod/myvideo.mp4?d=640x360">' or  
'<a href="rtmp://streaming.server.de/vod/myvideo.mp4#d=640x360">'

This example defines the video canvas to 640 pixel width and 360 pixel height. 

### Poster image for the video canvas

'<a href="rtmp://streaming.server.de/vod/myvideo.mp4><img src="http://moodle.server.com/image.png" /></a>'

