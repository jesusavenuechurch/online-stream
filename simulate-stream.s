#!/bin/bash

RTMP_URL="rtmp://localhost/live"
STREAM_KEY="stream_E7jJ1VlaPSMRBSmhrF6bNLRTMPpbz5Ua"  # Your actual key

echo "=========================================="
echo "Church Stream - OBS Simulator"
echo "=========================================="
echo ""

if ! command -v ffmpeg &> /dev/null; then
    echo "Error: FFmpeg is not installed"
    echo "Install with: brew install ffmpeg"
    exit 1
fi

echo "Stream Configuration:"
echo "RTMP URL: $RTMP_URL"
echo "Stream Key: $STREAM_KEY"
echo ""
echo "Starting simulated stream..."
echo "Press Ctrl+C to stop"
echo ""

# Simple test pattern without text
ffmpeg -re \
    -f lavfi -i "testsrc=size=1280x720:rate=30" \
    -f lavfi -i "sine=frequency=1000:sample_rate=44100" \
    -c:v libx264 -preset veryfast -maxrate 3000k -bufsize 6000k \
    -pix_fmt yuv420p -g 60 -c:a aac -b:a 128k -ar 44100 \
    -f flv "$RTMP_URL/$STREAM_KEY"

echo ""
echo "Stream ended
