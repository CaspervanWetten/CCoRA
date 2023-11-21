import StyleManagerAction from "./style-manager-action";
import Colors from "../colors";

export default class IncorrectEdgeStyle implements StyleManagerAction {
    public exec(context: CanvasRenderingContext2D): void {
        context.strokeStyle = Colors.red;
        context.lineWidth = 10;
    }
}
