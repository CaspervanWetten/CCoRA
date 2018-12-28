/// <reference path='../ResizingHTMLGenerator.ts'/>
/// <reference path='./SplitContainerSupervisor.ts'/>

class SplitContainer extends ResizingHTMLGenerator<HTMLElement>
{
    protected Left              : HTMLElement | HTMLGenerator<HTMLElement> | undefined;
    protected Right             : HTMLElement | HTMLGenerator<HTMLElement> | undefined;

    protected LeftElement       : HTMLElement | undefined;
    protected RightElement      : HTMLElement | undefined;
    protected Divider           : HTMLElement | undefined;
    
    // is really only just the x, as that's what we're only
    // interested in.
    protected DividerPosition   : number | undefined;
    protected DividerWidth      : number;

    public ClassnameDivider : string;
    public ClassnameLeft    : string;
    public ClassnameRight   : string;

    public constructor()
    {
        super();
        this.ClassPrefix        = "__SPLIT_CONTAINER__";
        this.ClassnameDivider   = "divider";
        this.ClassnameLeft      = "left";
        this.ClassnameRight     = "right";

        this.SetElementClassname("split");

        this.DividerPosition = undefined;
        this.DividerWidth    = 20; //px
        this.EventSupervisor = new SplitContainerSupervisor(this);
    }

    public SetLeft(element : HTMLElement | HTMLGenerator<HTMLElement>)
    {
        this.Left = element;
        if(this.LeftElement) {
            if(element instanceof HTMLElement) {
                this.LeftElement.innerHTML = element.outerHTML;
            }
            else if(element instanceof HTMLGenerator) {
                let oh = element.Render().outerHTML;
                this.LeftElement.innerHTML = oh;
            }
        }
        else {
            let div = document.createElement("div");
            let e = element instanceof HTMLElement ? element : element.Render();
            div.appendChild(e);
            this.AddClassname(div, this.ClassnameLeft);

            this.LeftElement = div;
        }
    }

    public SetRight(element : HTMLElement | HTMLGenerator<HTMLElement>)
    {
        this.Right = element;
        if(this.RightElement) {
            if(element instanceof HTMLElement) {
                this.RightElement.innerHTML = element.outerHTML;
            }
            else if(element instanceof HTMLGenerator) {
                let oh = element.Render().outerHTML;
                this.RightElement.innerHTML = oh;
            }
        }
        else {
            let div = document.createElement("div");
            let e = element instanceof HTMLElement ? element : element.Render();
            div.appendChild(e);
            this.AddClassname(div, this.ClassnameRight);

            this.RightElement = div;
        }
    }

    public AppendLeft(element : HTMLElement)
    {
        if(this.LeftElement) {
            this.LeftElement.appendChild(element);
        }
        else {
            this.SetLeft(element);
        }
    }

    public AppendRight(element : HTMLElement) {
        if(this.RightElement) {
            this.RightElement.appendChild(element);
        }
        else {
            this.SetRight(element);
        }
    }

    public GetLeft()
    {
        return this.LeftElement;
    }

    public GetRight()
    {
        return this.RightElement;
    }

    public GetDivider()
    {
        if(!this.Divider) {
            let div = document.createElement("div");
            div.style.width = this.DividerWidth + "px";
            this.AddClassname(div, this.ClassnameDivider);
            this.Divider = div;
        }
        return this.Divider;
    }

    public MoveDivider(newx : number)
    {
        if(this.Element) {
            let parent = this.Element.parentElement;
            if(newx >= 0 && newx + this.DividerWidth < parent.getBoundingClientRect().right) {
                let midx = this.DividerPosition + (this.DividerWidth / 2);
                let offset = newx - midx;
                let x = this.DividerPosition + offset + this.DividerWidth / 2;
                this.Divider.style.left = x.toString() + "px";

                this.LeftElement.style.width = newx.toString() + "px";
                this.RightElement.style.left = (newx + this.DividerWidth).toString() + "px";
                this.RightElement.style.width = (parent.getBoundingClientRect().width - newx - this.DividerWidth) + "px";

                this.DividerPosition = newx;

                if(this.Left instanceof ResizingHTMLGenerator) {
                    this.Left.Resize();
                }
                if(this.Right instanceof ResizingHTMLGenerator) {
                    this.Right.Resize();
                }
            }
        }
    }

    protected GenerateElement()
    {
        let container   = document.createElement("div");
        
        let left        = this.GetLeft();
        let right       = this.GetRight();
        let div         = this.GetDivider();

        container.appendChild(left);
        container.appendChild(div);
        container.appendChild(right);
        
        return container;
    }
    
    public Resize()
    {
        let ow = this.GetLeft().getBoundingClientRect().width;
        ow += this.GetRight().getBoundingClientRect().width;
        ow += this.DividerWidth;

        let parent = this.Element.parentElement;
        let p  = parent.getBoundingClientRect();
        let nw = p.width;
        let nh = p.height;
        let divfrac = this.DividerPosition / ow;
        this.DividerPosition = nw * divfrac;

        this.Element.style.width = nw.toString() + "px";
        this.Element.style.height = nh.toString() + "px";
        this.Divider.style.left = this.DividerPosition.toString() + "px";

        this.LeftElement.style.left = "0";
        this.LeftElement.style.width = (this.DividerPosition).toString() + "px";
        this.RightElement.style.left = (this.DividerPosition + this.DividerWidth).toString() + "px";
        this.RightElement.style.width = (nw - this.DividerPosition - this.DividerWidth).toString() + "px";
    }
}