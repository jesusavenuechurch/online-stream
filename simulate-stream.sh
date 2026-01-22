#!/bin/bash

# Church Stream - Mac Video Simulator with Debug

RTMP_URL="rtmp://stream.cesouthernafrica.co.za/live"
STREAM_KEY="stream_eoEkaKYzdY39z3jWg9uLu4LIZRJOrHnx"
VIDEO_FILE="$1"

echo "=========================================="
echo "Church Stream - Video Simulator"
echo "=========================================="
echo ""

# Check if FFmpeg is installed
if ! command -v ffmpeg &> /dev/null; then
    echo "❌ Error: FFmpeg is not installed"
    echo "Install with: brew install ffmpeg"
    exit 1
fi
echo "✅ FFmpeg found"

# Check if video file provided
if [ -z "$VIDEO_FILE" ]; then
    echo ""
    echo "No video file specified. Using test pattern instead."
    echo ""
    USE_TEST_PATTERN=true
else
    if [ ! -f "$VIDEO_FILE" ]; then
        echo "❌ Error: Video file not found: $VIDEO_FILE"
        exit 1
    fi
    echo "✅ Video file found: $VIDEO_FILE"
    USE_TEST_PATTERN=false
fi

# Check if nginx is running
if ! lsof -Pi :1935 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "❌ Error: Nginx RTMP server is not running on port 1935"
    echo ""
    echo "Start it with:"
    echo "  nginx"
    echo "  or: brew services start nginx-full"
    exit 1
fi
echo "✅ Nginx RTMP server is running on port 1935"

# Check if Laravel is running
if ! lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "⚠️  Warning: Laravel doesn't appear to be running on port 8000"
    echo "Stream key validation may fail!"
    echo ""
    echo "Start Laravel in another terminal:"
    echo "  php artisan serve"
    echo ""
    read -p "Continue anyway? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
else
    echo "✅ Laravel is running on port 8000"
fi

echo ""
echo "Stream Configuration:"
echo "  RTMP URL: $RTMP_URL"
echo "  Stream Key: ${STREAM_KEY:0:20}...${STREAM_KEY: -10}"
echo ""
echo "Starting stream..."
echo "Press Ctrl+C to stop"
echo ""

if [ "$USE_TEST_PATTERN" = true ]; then
    # Test pattern (no text overlay to avoid filter issues)
    ffmpeg -re \
        -f lavfi -i "testsrc=size=1280x720:rate=30" \
        -f lavfi -i "sine=frequency=1000:sample_rate=44100" \
        -c:v libx264 -preset veryfast -maxrate 3000k -bufsize 6000k \
        -pix_fmt yuv420p -g 60 \
        -c:a aac -b:a 128k -ar 44100 \
        -f flv "$RTMP_URL/$STREAM_KEY" 2>&1
else
    # Stream from video file (loops)
    ffmpeg -re -stream_loop -1 -i "$VIDEO_FILE" \
        -c:v libx264 -preset veryfast \
        -b:v 2500k -maxrate 3000k -bufsize 6000k \
        -pix_fmt yuv420p -g 60 \
        -c:a aac -b:a 128k -ar 44100 \
        -f flv "$RTMP_URL/$STREAM_KEY" 2>&1
fi

EXIT_CODE=$?

echo ""
if [ $EXIT_CODE -eq 0 ]; then
    echo "✅ Stream ended successfully"
else
    echo "❌ Stream ended with error (code: $EXIT_CODE)"
    echo ""
    echo "Common issues:"
    echo "  - Nginx not running: nginx or brew services start nginx-full"
    echo "  - Wrong stream key: Check in admin panel"
    echo "  - Laravel not running: php artisan serve"
    echo "  - Check nginx logs: tail -f /opt/homebrew/var/log/nginx/error.log"
fi
