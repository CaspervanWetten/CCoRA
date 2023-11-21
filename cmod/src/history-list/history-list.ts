import UndoableAction from "src/action/undoable-action";

export default class HistoryList {
    protected history: Array<UndoableAction>;
    protected pointer: number;
    protected hooks: Array<() => any>;

    public constructor() {
        this.history = new Array<UndoableAction>();
        this.pointer = this.history.length - 1;
        this.hooks = new Array<() => any>();
    }

    public exec(action: UndoableAction): void {
        action.exec();
        if (this.pointer == this.history.length - 1) {
            this.history.push(action);
        } else {
            this.history[this.pointer + 1] = action;
        }
        this.pointer++;
        this.history.splice(this.pointer + 1);
        this.execHooks();
    }

    public undo(): void {
        if (this.pointer >= 0) {
            let action = this.history[this.pointer];
            action.undo();
        }
        let oldPointer = this.pointer;
        this.pointer = Math.max(-1, this.pointer - 1);
        let newPointer = this.pointer;
        if (oldPointer !== newPointer) {
            this.execHooks();
        }
    }

    public redo(): void {
        let oldPointer = this.pointer;
        if (this.pointer < this.history.length - 1) {
            this.pointer = Math.min(this.pointer + 1, this.history.length - 1);
            let action = this.history[this.pointer];
            action.redo()
        }
        let newPointer = this.pointer;
        if (oldPointer !== newPointer) {
            this.execHooks();
        }
    }

    public addHook(f: () => any): void {
        this.hooks.push(f);
    }

    protected execHooks(): void {
        for (let i = 0; i < this.hooks.length; i++) {
            this.hooks[i]();
        }
    }
}
