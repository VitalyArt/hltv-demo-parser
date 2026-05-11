## Engine

This parser works with demo files from the **GoldSrc engine** — the game engine powering early Valve titles.

## Supported Games

| Game | Demo extension |
|---|---|
| Half-Life 1 | `.dem` |
| Counter-Strike 1.6 | `.dem` |
| Team Fortress Classic | `.dem` |
| Day of Defeat | `.dem` |
| Any GoldSrc-based mod | `.dem` |

## HLTV vs POV Demos

The parser supports both types of demo files:

- **HLTV demo** — recorded by an HLTV (Half-Life TV) proxy server. The filename typically contains a timestamp pattern (`-ymdHi-`), which the parser uses to extract `startTime`.
- **POV (Point of View) demo** — recorded locally by a player using the `record` command. These files usually do not have a timestamp in the filename, so `getStartTime()` returns `null`.

## Demo File Structure

A `.dem` file consists of:

1. **Header** — 8-byte magic sequence `HLDEMO`, followed by demo protocol version (offset 8), network protocol version (offset 12), map name (offset 16), game directory (offset 276), map CRC (offset 536), and directory offset (offset 540).
2. **Entry table** — array of directory entries, each 92 bytes:
   - Entry number (4), title string (64), flags (4), CD track (4), track time (4), frame count (4), data offset (4), data length (4)
3. **Data segments** — each entry points to a segment containing sequential macro blocks:
   - **Loading segment** — map signon data with server messages, terminated by a `LAST_IN_SEGMENT` marker.
   - **Playback segment** — recorded gameplay frames with network update messages.

The parser extracts all entries and their metadata (type, description, flags, CD track, track time, frames, offset, file length).  
For `LOADING` entries, the parser additionally parses the macro block headers (`DemoFrame[]`) from the data segment.  
`PLAYBACK` frame parsing requires the full HLTV network protocol and is not implemented.
