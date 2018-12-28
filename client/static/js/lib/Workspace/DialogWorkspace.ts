/// <reference path='./index.ts'/>

/// <reference path='../Action/index.ts'/>
/// <reference path='../HTMLGenerators/index.ts'/>

abstract class DialogWorkspace<W extends DialogWorkspace<W>> extends Workspace<W>
{
    public Dialog : Dialog;

    public constructor()
    {
        super();
        this.Dialog = new Dialog();
    }
}