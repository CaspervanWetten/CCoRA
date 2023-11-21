import StyleManagerAction from "./style-manager-action";
import Colors from "../colors";

export default class CorrectEdgeStyle implements StyleManagerAction {
    public exec(context: CanvasRenderingContext2D): void {
        context.strokeStyle = Colors.green;
        context.lineWidth = 10;
    }
}
