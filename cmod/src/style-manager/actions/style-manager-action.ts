import Colors from "../colors";

export default interface StyleManagerAction {
    exec(context: CanvasRenderingContext2D): void;
}
