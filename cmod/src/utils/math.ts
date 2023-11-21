let clamp = function(x: number, min: number, max: number) {
    return Math.max(min, Math.min(x, max));
}

let remainder = function(x: number, mod: number) {
    let result = x % mod;
    if (result < 0) {
        result = mod + result;
    }
    return result;
}

export { clamp, remainder }
