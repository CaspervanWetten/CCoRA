export default interface Snappable {
    snap(hgrid: number, vgrid: number, context: CanvasRenderingContext2D): void;
}

export function isSnappable(object: any): object is Snappable {
    return 'snap' in object;
}
