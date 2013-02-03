
function QuotationsCanvas(qcanvas, quotes)
{
    this.status = qcanvas.find("div")[0];
    this.canvas = qcanvas.find("canvas")[0];
    this.LINE_WIDTH = 3;
    this.LINE_SKIP = 7; // pixels between lines
    this.HEADER_HEIGHT = 15; // where actual diagram begins
    this.quotations = quotes; // quotations by id
    this.isel = null; // index of selected

    qlist = []; // sorted quotations
    for (qid in quotes) qlist.push(quotes[qid]);
    if (qlist.length == 0) return;
    // sort by start date
    qlist.sort(function(q1,q2) {
	return q1.start_time > q2.start_time;
    });
    this.qlist = qlist
    // find the period of all quotes (in seconds since 1. Jan 1970 -- TODO)
    this.periodStartTime = qlist[0].start_time.getTime();
    periodEnd = qlist.reduce(function(a,b) {return (a.end_time>b.end_time) ? a : b});
    this.periodEndTime = periodEnd.end_time.getTime();
    this.period = this.periodEndTime - this.periodStartTime;

    this.draw();
    $(this.canvas).mousedown(this, this.onMouseDown);
}

QuotationsCanvas.prototype.monthNames = [
    "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь",
    "Октябрь", "Ноябрь", "Декабрь"
];

QuotationsCanvas.prototype.draw = function() {
    // draw
    w = this.canvas.width;
    c = this.canvas.getContext("2d");

    // clear
    c.fillStyle = "#ffffff";
    c.rect(0,0,this.canvas.width,this.canvas.height);
    c.fill();

    // find/draw timeline ticks and labels
    start = new Date(this.periodStartTime)
    end = new Date(this.periodEndTime)
    c.strokeStyle = "#000000";
    c.beginPath();
    c.moveTo(0,0);
    c.lineTo(w,0);
    c.stroke();
    for (cur = new Date(start.getYear()); cur <= end; ) {
	if (cur >= start) {
	    // draw tick
	    TICK_LENGTH = 5;
	    if (cur.getMonth()==0) TICK_LENGTH *= 2;
	    tickX = (cur.getTime()-this.periodStartTime)*w/this.period;
	    c.beginPath()
	    c.moveTo(tickX, 0)
	    c.lineTo(tickX, TICK_LENGTH)
	    c.stroke()
	    // text
	    c.fillStyle="#000000";
	    if (cur.getMonth()==0) {
		c.fillText(""+cur.getFullYear(),tickX,this.HEADER_HEIGHT);
	    } else {
		c.fillText(this.monthNames[cur.getMonth()],tickX,this.HEADER_HEIGHT);
	    }
	}

	// increment
	if (cur.getMonth()<11) {
	    cur.setMonth(cur.getMonth()+1)
	} else {
	    cur.setYear(cur.getYear()+1)
	    cur.setMonth(0)
	}
    }

    c.save() // save context before shifting
    c.translate(0,this.HEADER_HEIGHT)

    // for each quote
    for (i=0; i<qlist.length; i++) {
	lineY = i*this.LINE_SKIP+this.LINE_SKIP/2;
	quote = qlist[i];
	x1 = (quote.start_time.getTime()-this.periodStartTime)*w/this.period;
	x2 = (quote.end_time.getTime()-this.periodStartTime)*w/this.period;
	// minimum line length
	x2 = Math.max(x1+8, x2);
	// line
	if (this.isel == i) c.strokeStyle = "#ffd000"; else c.strokeStyle = "#5070e0";
	c.beginPath();
	c.lineWidth = this.LINE_WIDTH;
	c.moveTo(x1, lineY);
	c.lineTo(x2, lineY);
	c.stroke();
	// period booundary ticks
	c.strokeStyle = "#000000";
	c.lineWidth = 2;
	c.beginPath();
	c.moveTo(x1, lineY - this.LINE_WIDTH/2-1);
	c.lineTo(x1, lineY + this.LINE_WIDTH/2+1);
	c.stroke();
	c.beginPath();
	c.moveTo(x2, lineY - this.LINE_WIDTH/2-1);
	c.lineTo(x2, lineY + this.LINE_WIDTH/2+1);
	c.stroke();
    }
    c.restore() // restore old shift
}

// logical position: 0 = above the center of the first line, 1.5 = between 2. and 3. lines
QuotationsCanvas.prototype.quotationPosFromCoords = function(globx,globy) {
    offset = $(this.canvas).offset();
    x = (globx-offset.left);
    y = (globy-offset.top);
    pos = (y-this.LINE_SKIP/2-this.HEADER_HEIGHT)/this.LINE_SKIP;
    return pos;
}

QuotationsCanvas.prototype.onMouseDown = function(e) {
    qcanvas = e.data;
    pos = qcanvas.quotationPosFromCoords(e.pageX, e.pageY)
    i=Math.round(pos)
    if (i>=0 && i<qcanvas.qlist.length) {
	if (qcanvas.isel != i) {
	    // get list element with quot id
	    elem = $("#q"+qcanvas.qlist[i].id);
	    qcanvas.status.innerHTML = elem.html();
	    $(qcanvas.status).children().show();
	    qcanvas.isel = i;
	    qcanvas.draw();
	}
    }
}