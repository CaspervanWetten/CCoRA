/// <reference path='./index.ts'/>
/// <reference path='../Dialog/index.ts'/>

class Popup extends Dialog
{
    protected ClassnameWrapper      : string;
    protected ClassnameBackdrop     : string;
    protected ClassnameDialog       : string;
    protected ClassnameCloseButton  : string;

    protected Closeable             : boolean;

    protected Top                   : number | undefined;
    protected Left                  : number | undefined;

    protected Dialog                : HTMLElement | undefined;

    public constructor(left : undefined | number = undefined, top : undefined | number = undefined)
    {
        super();
        this.ClassPrefix            = "__POPUP__";
        this.ClassnameWrapper       = "wrapper";
        this.ClassnameBackdrop      = "backdrop";
        this.ClassnameDialog        = "popup";
        this.ClassnameCloseButton   = "close";

        this.Closeable              = true;
        this.Left                   = left;
        this.Top                    = top;
    }

    protected GenerateElement()
    {
        let dialog = super.GenerateElement();
        this.AddClassname(dialog, this.ClassnameDialog);
        this.Dialog = dialog;

        if(this.Top) {
            dialog.style.top  = this.Top.toString() + "px";
        }

        if(this.Left) {
            dialog.style.left = this.Left.toString() + "px";
        }

        let wrapper = this.GenerateWrapper();
        let backdrop = this.GenerateBackdrop();

        wrapper.appendChild(backdrop);

        if(this.Closeable) {
            let button = this.GenerateCloseButton();
            dialog.appendChild(button);
            backdrop.addEventListener("click", this.Remove.bind(this));            
        }

        wrapper.appendChild(dialog);

        return wrapper;
    }

    protected GenerateWrapper()
    {
        let wrapper = document.createElement("div");
        this.SetClassname(wrapper, this.ClassnameWrapper);
        return wrapper;
    }

    protected GenerateBackdrop()
    {
        let result = document.createElement("div");
        this.SetClassname(result, this.ClassnameBackdrop);
        return result;
    }

    protected GenerateCloseButton()
    {
        let button = document.createElement("span");
        button.appendChild(document.createTextNode("X"));
        button.addEventListener("click", this.Remove.bind(this));

        this.AddClassname(button, this.ClassnameCloseButton);

        return button;
    }

    //#region Getters and Setters
    public GetCloseable()
    {
        return this.Closeable;
    }

    public SetCloseable(b : boolean)
    {
        this.Closeable = b;
    }

    public GetClassnameWrapper()
    {
        return this.ClassnameWrapper;
    }

    public SetClassnameWrapper(name : string)
    {
        this.ClassnameWrapper = name;
    }

    public GetClassnameBackdrop()
    {
        return this.ClassnameBackdrop;
    }

    public SetClassnameBackdrop(name : string)
    {
        this.ClassnameBackdrop = name;
    }

    public GetClassnameDialog()
    {
        return this.ClassnameDialog;
    }

    public SetClassnameDialog(name : string)
    {
        this.ClassnameDialog = name;
    }

    public GetClassnameCloseButton()
    {
        return this.ClassnameCloseButton;
    }

    public SetClassnameCloseButton(name : string)
    {
        this.ClassnameCloseButton = name;
    }
    
    public GetLeft()
    {
        return this.Left;
    }

    public SetLeft(left : number)
    {
        this.Left = left;
        if(this.Element != null) {
            let d = this.Dialog;
            d.style.left = this.Left + "px";
        } 
    }

    public GetTop()
    {
        return this.Top;
    }
    
    public SetTop(top : number)
    {
        this.Top = top;
        if(this.Element != null) {
            let d = this.Dialog;
            d.style.top = this.Top + "px";
        }
    }
    //#endregion
}