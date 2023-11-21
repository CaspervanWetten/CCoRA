import Action from "./action";

export default interface UndoableAction extends Action {
    undo(): void;
    redo(): void;
}
