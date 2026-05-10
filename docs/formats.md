## Supported Formats

### Engine

This parser works with demo files from the **GoldSrc engine** — the game engine powering early Valve titles.

### Supported Games

| Game | Demo extension |
|---|---|
| Half-Life 1 | `.dem` |
| Counter-Strike 1.6 | `.dem` |
| Team Fortress Classic | `.dem` |
| Day of Defeat | `.dem` |
| Any GoldSrc-based mod | `.dem` |

### HLTV vs POV Demos

The parser supports both types of demo files:

- **HLTV demo** — recorded by an HLTV (Half-Life TV) proxy server. The filename typically contains a timestamp pattern (`-ymdHi-`), which the parser uses to extract `startTime`.
- **POV (Point of View) demo** — recorded locally by a player using the `record` command. These files usually do not have a timestamp in the filename, so `getStartTime()` returns `null`.

### Demo File Structure

A `.dem` file consists of:

1. **Header** — 8-byte magic sequence `HLDEMO`, followed by demo protocol version, network protocol version, map name, and client/game directory name.
2. **Entries** — each entry represents a segment of the demo:
   - **Loading entry** — contains the map loading data
   - **Playback entry** — contains the actual recorded gameplay data

The parser extracts all entries and their metadata (type, description, flags, frames, offset, file length).
