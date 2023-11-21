export default function isNumber(val: string) {
    return /^-?[0-9]*$/.test(val);
}
