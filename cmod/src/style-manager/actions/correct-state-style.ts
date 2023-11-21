import StyleManagerAction from "./style-manager-action";
import Colors from "../colors";

export default class CorrectStateStyle implements StyleManagerAction {
    public exec(context: CanvasRenderingContext2D): void {
        context.strokeStyle = Colors.green;
        context.fillStyle = "white";
        context.lineWidth = 16;
    }
}
